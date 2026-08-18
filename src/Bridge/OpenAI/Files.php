<?php

namespace OneToMany\AI\Bridge\OpenAI;

use OneToMany\AI\Bridge\OpenAI\Payload\File as FilePayload;
use OneToMany\AI\Contract\Bridge\FilesProviderInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\Files\LocalFile;
use OneToMany\AI\Resource\Files\RemoteFile;

use function fclose;
use function fopen;
use function sprintf;

final readonly class Files implements FilesProviderInterface
{
    public function __construct(
        private Transport $transport,
    ) {
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\FilesProviderInterface
     */
    #[\Override]
    public function provider(): Provider
    {
        return Provider::OpenAI;
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\FilesProviderInterface
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

        $url = $this->transport->url('files');

        try {
            $payload = $this->transport->requestObject('POST', $url, FilePayload::class, [
                'body' => [
                    'file' => $handle,
                    'purpose' => 'user_data',
                ],
            ])->payload;
        } finally {
            @fclose($handle);
        }

        try {
            return new RemoteFile($this->provider(), $payload->id, $file->mediaType, null, $payload->expiresAt, $payload->purpose);
        } catch (\Throwable $e) {
            throw new RuntimeException('OpenAI returned an invalid file response.', previous: $e);
        }
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\FilesProviderInterface
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

        $this->transport->request('DELETE', $this->transport->url('files', $file->id));
    }
}
