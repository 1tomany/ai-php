<?php

namespace OneToMany\AI\Resource\File;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Provider;

use function stripos;
use function trim;

final readonly class RemoteFile
{
    public Provider $provider;

    /**
     * @var non-empty-string
     */
    public string $id;

    /**
     * @var non-empty-lowercase-string
     */
    public string $mimeType;

    /**
     * @param ?non-empty-string $mimeType
     * @param ?non-empty-string $uri
     * @param ?non-empty-string $purpose
     *
     * @throws InvalidArgumentException when the file ID is empty
     * @throws InvalidArgumentException when the file MIME type is empty
     */
    public function __construct(
        string|Provider $provider,
        ?string $id,
        ?string $mimeType = null,
        public ?string $uri = null,
        public ?\DateTimeImmutable $expiresAt = null,
        public ?string $purpose = null,
    ) {
        $this->provider = Provider::create($provider);

        if ('' === $id = trim((string) $id)) {
            throw new InvalidArgumentException('The file ID cannot be empty.');
        }

        $this->id = $id;

        if ('' === $mimeType = trim((string) $mimeType)) {
            throw new InvalidArgumentException('The MIME type cannot be empty.');
        }

        $this->mimeType = \strtolower($mimeType);
    }

    public function isImage(): bool
    {
        return \str_starts_with($this->mimeType, 'image/');
    }

    public function isVideo(): bool
    {
        return \str_starts_with($this->mimeType, 'video/');
    }
}
