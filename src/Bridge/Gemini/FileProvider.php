<?php

namespace OneToMany\AI\Bridge\Gemini;

use OneToMany\AI\Bridge\Gemini\Payload\Files\File;
use OneToMany\AI\Bridge\Gemini\Payload\Files\Upload;
use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Bridge\FileProviderInterface;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\File\LocalFile;
use OneToMany\AI\Resource\File\RemoteFile;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;

use function fclose;
use function feof;
use function fopen;
use function fread;
use function is_numeric;
use function max;
use function sprintf;
use function strlen;

final readonly class FileProvider implements FileProviderInterface
{
    /**
     * Default granularity (8MB) of each file chunk.
     *
     * @var positive-int
     */
    private const int DEFAULT_CHUNK_GRANULARITY = 8 * 1024 * 1024;

    public function __construct(
        private Transport $transport,
        private string $apiVersion = 'v1beta',
    ) {
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\FileProviderInterface
     */
    #[\Override]
    public function provider(): Provider
    {
        return Provider::Gemini;
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\FileProviderInterface
     * @see OneToMany\AI\Bridge\Transport::request()
     *
     * @throws RuntimeException when no resumable upload URL is returned
     * @throws RuntimeException when opening the file fails
     * @throws RuntimeException when reading the file fails
     * @throws RuntimeException when the server did not receive any file data
     */
    #[\Override]
    public function upload(LocalFile $file): RemoteFile
    {
        $url = $this->transport->url('upload', $this->apiVersion, 'files');

        $start = $this->transport->postRequest($url, [
            'headers' => [
                'x-goog-upload-command' => 'start',
                'x-goog-upload-header-content-length' => $file->size,
                'x-goog-upload-header-content-type' => $file->mediaType,
                'x-goog-upload-protocol' => 'resumable',
            ],
            'json' => [
                'file' => [
                    'display_name' => $file->name,
                ],
            ],
        ]);

        $headers = $this->transport->headers($start);

        if (!isset($headers['x-goog-upload-url'][0])) {
            throw new RuntimeException('Gemini did not return a resumable upload URL.');
        }

        /** @var non-empty-string $uploadUrl */
        $uploadUrl = $headers['x-goog-upload-url'][0];

        // Determine the number of bytes in each uploaded chunk
        if (isset($headers['x-goog-upload-chunk-granularity'][0])) {
            $chunkSize = $headers['x-goog-upload-chunk-granularity'][0];
        }

        if (!isset($chunkSize) || !is_numeric($chunkSize)) {
            $chunkSize = self::DEFAULT_CHUNK_GRANULARITY;
        }

        $chunkSize = max(1, (int) $chunkSize);

        if (false === $handle = @fopen($file->path, 'rb')) {
            throw new RuntimeException(sprintf('Opening the file "%s" failed.', $file->path));
        }

        $response = null;

        try {
            $offset = 0;

            while (!feof($handle)) {
                $command = 'upload';

                if (false === $chunk = fread($handle, $chunkSize)) {
                    throw new RuntimeException(sprintf('Reading the file "%s" failed.', $file->path));
                }

                $length = strlen($chunk);

                if (!$length) {
                    break;
                }

                if ($offset + $length >= $file->size) {
                    $command = "{$command}, finalize";
                }

                $response = $this->transport->postRequest($uploadUrl, [
                    'headers' => [
                        'content-length' => $length,
                        'x-goog-upload-command' => $command,
                        'x-goog-upload-offset' => $offset,
                    ],
                    'body' => $chunk,
                ]);

                $offset += $length;
            }

            if (null === $response) {
                throw new RuntimeException(sprintf('The %s server did not receive any file data.', $this->provider()->getName()));
            }

            $record = $this->transport->decode($response, File::class, [
                UnwrappingDenormalizer::UNWRAP_PATH => '[file]',
            ]);
        } finally {
            @fclose($handle);
        }

        return new RemoteFile($this->provider(), $record->name, $record->mimeType, $record->uri, $record->expirationTime);
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\FileProviderInterface
     */
    #[\Override]
    public function delete(RemoteFile $file): void
    {
        $this->transport->deleteRequest($this->transport->url($this->apiVersion, $file->id));
    }
}
