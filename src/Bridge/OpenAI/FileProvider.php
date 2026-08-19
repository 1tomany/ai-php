<?php

namespace OneToMany\AI\Bridge\OpenAI;

use OneToMany\AI\Bridge\OpenAI\Payload\File;
use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Bridge\FileProviderInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\File\LocalFile;
use OneToMany\AI\Resource\File\RemoteFile;

use function fclose;
use function fopen;
use function sprintf;

final readonly class FileProvider implements FileProviderInterface
{
    public function __construct(
        private Transport $transport,
        private string $apiVersion = 'v1',
    ) {
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\FileProviderInterface
     */
    #[\Override]
    public function provider(): Provider
    {
        return Provider::OpenAI;
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\FileProviderInterface
     *
     * @throws RuntimeException when opening the file fails
     * @throws RuntimeException when an invalid response is returned
     */
    #[\Override]
    public function upload(LocalFile $file): RemoteFile
    {
        if (false === $handle = @fopen($file->path, 'rb')) {
            throw new RuntimeException(sprintf('Opening the file "%s" failed.', $file->path));
        }

        $url = $this->transport->url($this->apiVersion, 'files');

        try {
            $response = $this->transport->request('POST', $url, [
                'body' => [
                    'file' => $handle,
                    'purpose' => 'user_data',
                ],
            ]);

            $record = $this->transport->decode($response, File::class);
        } finally {
            @fclose($handle);
        }

        return new RemoteFile($this->provider(), $record->id, $file->mediaType, null, $record->expires_at, $record->purpose);
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\FileProviderInterface
     *
     * @throws InvalidArgumentException when the file belongs to another provider
     * @throws RuntimeException when deleting the file fails
     */
    #[\Override]
    public function delete(RemoteFile $file): void
    {
        if (Provider::OpenAI !== $file->provider) {
            throw new InvalidArgumentException('The OpenAI files provider can only delete OpenAI files.');
        }

        $this->transport->request('DELETE', $this->transport->url($this->apiVersion, 'files', $file->id));
    }
}
