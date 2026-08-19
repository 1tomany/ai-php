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
     */
    #[\Override]
    public function upload(LocalFile $file): RemoteFile
    {
        if (false === $handle = @fopen($file->path, 'rb')) {
            throw new RuntimeException(sprintf('Opening the file "%s" failed.', $file->path));
        }

        $url = $this->transport->url($this->apiVersion, 'files');

        try {
            $response = $this->transport->postRequest($url, [
                'body' => [
                    'file' => $handle,
                    'purpose' => 'user_data',
                ],
            ]);

            $record = $this->transport->decode($response, File::class);
        } finally {
            @fclose($handle);
        }

        return new RemoteFile($this->provider(), $record->id, $file->mediaType, null, $record->getExpiresAt(), $record->purpose);
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\FileProviderInterface
     */
    #[\Override]
    public function delete(RemoteFile $file): void
    {
        $this->transport->deleteRequest($this->transport->url($this->apiVersion, 'files', $file->id));
    }
}
