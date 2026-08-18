<?php

namespace OneToMany\AI\Bridge\OpenAI;

use OneToMany\AI\Bridge\FilesProviderInterface;
use OneToMany\AI\Bridge\OpenAI\Payload\File as FilePayload;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\File\LocalFile;
use OneToMany\AI\File\RemoteFile;
use OneToMany\AI\Provider;

use function fclose;
use function fopen;
use function rawurlencode;
use function sprintf;

final readonly class Files implements FilesProviderInterface
{
    public function __construct(
        private Transport $transport,
    ) {
    }

    #[\Override]
    public function provider(): Provider
    {
        return Provider::OpenAI;
    }

    #[\Override]
    public function upload(LocalFile $file): RemoteFile
    {
        if (false === $handle = @fopen($file->path, 'rb')) {
            throw new RuntimeException(sprintf('Opening the file "%s" failed.', $file->path));
        }

        try {
            $payload = $this->transport->requestObject('POST', $this->transport->url('files'), FilePayload::class, [
                'body' => [
                    'file' => $handle,
                    'purpose' => 'user_data',
                ],
            ])->payload;
        } finally {
            @fclose($handle);
        }

        try {
            $expiresAt = null !== $payload->expiresAt
                ? \DateTimeImmutable::createFromTimestamp($payload->expiresAt)
                : null;

            return new RemoteFile(
                provider: Provider::OpenAI,
                id: $payload->id,
                mediaType: $file->mediaType,
                expiresAt: $expiresAt,
                purpose: $payload->purpose,
            );
        } catch (\Throwable $e) {
            throw new RuntimeException('OpenAI returned an invalid file response.', previous: $e);
        }
    }

    #[\Override]
    public function delete(RemoteFile $file): void
    {
        if (Provider::OpenAI !== $file->provider) {
            throw new InvalidArgumentException('The OpenAI files provider can only delete OpenAI files.');
        }

        $this->transport->request(
            'DELETE',
            $this->transport->url('files', rawurlencode($file->id)),
        );
    }
}
