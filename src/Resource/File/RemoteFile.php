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
     * @param ?non-empty-string $mediaType
     * @param ?non-empty-string $uri
     * @param ?non-empty-string $purpose
     *
     * @throws InvalidArgumentException when the file ID is empty
     * @throws InvalidArgumentException when the file media type is empty
     */
    public function __construct(
        string|Provider $provider,
        ?string $id,
        public ?string $mediaType = null,
        public ?string $uri = null,
        public ?\DateTimeImmutable $expiresAt = null,
        public ?string $purpose = null,
    ) {
        $this->provider = Provider::create($provider);

        if ('' === $id = trim((string) $id)) {
            throw new InvalidArgumentException('The file ID cannot be empty.');
        }

        $this->id = $id;
    }

    /**
     * @phpstan-assert-if-true non-empty-string $this->mediaType
     */
    public function isAudio(): bool
    {
        return null !== $this->mediaType && 0 === stripos($this->mediaType, 'audio/');
    }

    /**
     * @phpstan-assert-if-true non-empty-string $this->mediaType
     */
    public function isImage(): bool
    {
        return null !== $this->mediaType && 0 === stripos($this->mediaType, 'image/');
    }

    /**
     * @phpstan-assert-if-true non-empty-string $this->mediaType
     */
    public function isVideo(): bool
    {
        return null !== $this->mediaType && 0 === stripos($this->mediaType, 'video/');
    }
}
