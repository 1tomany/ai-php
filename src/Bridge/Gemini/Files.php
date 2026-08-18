<?php

namespace OneToMany\AI\Bridge\Gemini;

use OneToMany\AI\Bridge\FilesProviderInterface;
use OneToMany\AI\Bridge\Gemini\Payload\FileResponse;
use OneToMany\AI\Contract\Exception\ExceptionInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\File\LocalFile;
use OneToMany\AI\File\RemoteFile;
use OneToMany\AI\Provider;

use function fclose;
use function feof;
use function fopen;
use function fread;
use function is_numeric;
use function is_string;
use function max;
use function sprintf;
use function strlen;
use function trim;

final readonly class Files implements FilesProviderInterface
{
    private const int DEFAULT_CHUNK_SIZE = 8 * 1024 * 1024;

    public function __construct(
        private Transport $transport,
        private string $apiVersion = 'v1beta',
    ) {
    }

    #[\Override]
    public function provider(): Provider
    {
        return Provider::Gemini;
    }

    #[\Override]
    public function upload(LocalFile $file): RemoteFile
    {
        $start = $this->transport->request('POST', $this->transport->url('upload', $this->apiVersion, 'files'), [
            'headers' => [
                'x-goog-upload-command' => 'start',
                'x-goog-upload-header-content-length' => $file->size,
                'x-goog-upload-header-content-type' => $file->mediaType,
                'x-goog-upload-protocol' => 'resumable',
            ],
            'json' => [
                'file' => ['display_name' => $file->name],
            ],
        ]);

        $headers = $this->transport->headers($start);
        $uploadUrl = $headers['x-goog-upload-url'][0] ?? null;

        if (!is_string($uploadUrl) || '' === trim($uploadUrl)) {
            throw new RuntimeException('Gemini did not return a resumable upload URL.');
        }

        $chunkSize = $headers['x-goog-upload-chunk-granularity'][0] ?? null;
        $chunkSize = is_numeric($chunkSize) ? max(1, (int) $chunkSize) : self::DEFAULT_CHUNK_SIZE;

        if (false === $handle = @fopen($file->path, 'rb')) {
            throw new RuntimeException(sprintf('Opening the file "%s" failed.', $file->path));
        }

        $offset = 0;
        $response = null;

        try {
            while (!feof($handle)) {
                $chunk = fread($handle, $chunkSize);

                if (false === $chunk) {
                    throw new RuntimeException(sprintf('Reading the file "%s" failed.', $file->path));
                }

                if ('' === $chunk) {
                    break;
                }

                $length = strlen($chunk);
                $final = $offset + $length >= $file->size;

                $response = $this->transport->request('POST', $uploadUrl, [
                    'headers' => [
                        'content-length' => $length,
                        'x-goog-upload-command' => $final ? 'upload, finalize' : 'upload',
                        'x-goog-upload-offset' => $offset,
                    ],
                    'body' => $chunk,
                ]);

                $offset += $length;
            }
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Reading the file "%s" failed.', $file->path), previous: $e);
        } finally {
            @fclose($handle);
        }

        if (null === $response) {
            throw new RuntimeException('Gemini did not receive any file data.');
        }

        $payload = $this->transport->decode($response, FileResponse::class)->payload->file;

        try {
            $expiration = null !== $payload->expirationTime
                ? new \DateTimeImmutable($payload->expirationTime)
                : null;

            return new RemoteFile(
                provider: Provider::Gemini,
                id: $payload->name,
                mediaType: '' !== trim($payload->mimeType) ? $payload->mimeType : $file->mediaType,
                uri: $payload->uri,
                expiresAt: $expiration,
            );
        } catch (\Throwable $e) {
            throw new RuntimeException('Gemini returned an invalid file response.', previous: $e);
        }
    }

    #[\Override]
    public function delete(RemoteFile $file): void
    {
        if (Provider::Gemini !== $file->provider) {
            throw new InvalidArgumentException('The Gemini files provider can only delete Gemini files.');
        }

        $this->transport->request(
            'DELETE',
            $this->transport->url($this->apiVersion, $file->id),
        );
    }
}
