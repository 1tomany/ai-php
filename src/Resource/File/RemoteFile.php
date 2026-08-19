<?php

namespace OneToMany\AI\Resource\File;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Provider;

use function is_int;
use function is_string;
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
     * @var ?non-empty-string
     */
    public ?string $uri;

    public ?\DateTimeImmutable $expiresAt;

    /**
     * @var ?non-empty-string
     */
    public ?string $purpose;

    /**
     * @throws InvalidArgumentException when the file ID is empty
     * @throws InvalidArgumentException when the file media type is empty
     */
    public function __construct(
        public Provider $provider,
        string $id,
        string $mediaType,
        ?string $uri = null,
        int|string|\DateTimeImmutable|null $expiresAt = null,
        ?string $purpose = null,
    ) {
        if ('' === $id = trim($id)) {
            throw new InvalidArgumentException('The file ID cannot be empty.');
        }

        $this->id = $id;

        if ('' === $mediaType = trim($mediaType)) {
            throw new InvalidArgumentException('The media type cannot be empty.');
        }

        $this->mediaType = $mediaType;

        if (null !== $uri) {
            $uri = trim($uri);
        }

        $this->uri = '' !== $uri ? $uri : null;

        try {
            if (is_int($expiresAt)) {
                $expiresAt = \DateTimeImmutable::createFromTimestamp($expiresAt);
            } elseif (is_string($expiresAt)) {
                $expiresAt = new \DateTimeImmutable($expiresAt);
            }
        } catch (\DateException) {
            $expiresAt = null;
        }

        $this->expiresAt = $expiresAt;

        if (null !== $purpose) {
            $purpose = trim($purpose);
        }

        $this->purpose = '' !== $purpose ? $purpose : null;
    }
}
