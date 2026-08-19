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
        Provider $provider,
        ?string $id,
        public ?string $mediaType = null,
        public ?string $uri = null,
        public ?\DateTimeImmutable $expiresAt = null,
        public ?string $purpose = null,
    ) {
        $this->provider = $provider;

        if ('' === $id = trim((string) $id)) {
            throw new InvalidArgumentException('The file ID cannot be empty.');
        }

        $this->id = $id;
    }

    public function isAudio(): bool
    {
        return 0 === stripos($this->mediaType, 'audio/');
    }

    public function isImage(): bool
    {
        return 0 === stripos($this->mediaType, 'image/');
    }

    public function isVideo(): bool
    {
        return 0 === stripos($this->mediaType, 'video/');
    }
}
