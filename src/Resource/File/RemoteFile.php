<?php

namespace OneToMany\AI\Resource\File;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Provider;

use function trim;

final readonly class RemoteFile
{
    /**
     * @var non-empty-string
     */
    public string $id;

    /**
     * @var non-empty-string
     */
    public string $mediaType;

    /**
     * @param ?non-empty-string $uri
     * @param ?non-empty-string $purpose
     *
     * @throws InvalidArgumentException when the file ID is empty
     * @throws InvalidArgumentException when the file media type is empty
     */
    public function __construct(
        public Provider $provider,
        string $id,
        string $mediaType,
        public ?string $uri = null,
        public ?\DateTimeImmutable $expiresAt = null,
        public ?string $purpose = null,
    ) {
        if ('' === $id = trim($id)) {
            throw new InvalidArgumentException('The file ID cannot be empty.');
        }

        $this->id = $id;

        if ('' === $mediaType = trim($mediaType)) {
            throw new InvalidArgumentException('The media type cannot be empty.');
        }

        $this->mediaType = $mediaType;
    }
}
