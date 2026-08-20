<?php

namespace OneToMany\AI\Bridge\OpenAI;

use OneToMany\AI\Bridge\OpenAI\Response\File\File;
use OneToMany\AI\Contract\Bridge\FileProviderInterface;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Resource\File\LocalFile;
use OneToMany\AI\Resource\File\RemoteFile;

use function fclose;
use function fopen;
use function sprintf;

final readonly class FileProvider extends AbstractProvider implements FileProviderInterface
{
    /**
     * @see OneToMany\AI\Contract\Bridge\FileProviderInterface
     *
     * @throws RuntimeException when opening the file fails
     */
    #[\Override]
    public function upload(LocalFile $file): RemoteFile
    {
        if (false === $handle = @fopen($file->getPath(), 'rb')) {
            throw new RuntimeException(sprintf('Opening the file "%s" failed.', $file->getPath()));
        }

        $url = $this->url('files');

        try {
            $response = $this->transport->postRequest($url, [
                'auth_bearer' => $this->getApiKey(),
                'body' => [
                    'file' => $handle,
                    'purpose' => 'user_data',
                ],
            ]);

            $record = $this->transport->decode($response, File::class);
        } finally {
            @fclose($handle);
        }

        return new RemoteFile(static::getVendor(), $record->id, null, $file->getMimeType(), $record->getExpiresAt(), $record->purpose);
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\FileProviderInterface
     */
    #[\Override]
    public function delete(RemoteFile $file): void
    {
        $url = $this->url('files', $file->getId());

        $this->transport->deleteRequest($url, [
            'auth_bearer' => $this->getApiKey(),
        ]);
    }
}
