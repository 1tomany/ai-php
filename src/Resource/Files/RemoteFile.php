<?php

namespace OneToMany\AI\Resource\Files;

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
     * @var ?non-empty-string
     */
    public ?string $uri;

    public ?\DateTimeImmutable $expiresAt;

    /**
     * @var ?non-empty-string
     */
    public ?string $purpose;

    /**
     * @throws InvalidArgumentException when the remote file ID is empty
     * @throws InvalidArgumentException when the media type is empty
     * @throws InvalidArgumentException when a Gemini file has no URI
     */
    public function __construct(
        public Provider $provider,
        string $id,
        string $mediaType,
        ?string $uri = null,
        int|\DateTimeImmutable|null $expiresAt = null,
        ?string $purpose = null,
    ) {
        if ('' === $id = trim($id)) {
            throw new InvalidArgumentException('The remote file ID cannot be empty.');
        }

        $this->id = $id;

        if ('' === $mediaType = trim($mediaType)) {
            throw new InvalidArgumentException('The remote file media type cannot be empty.');
        }

        $this->mediaType = $mediaType;

        if (null !== $uri) {
            $uri = trim($uri);
        }

        if ($provider->isGemini() && '' === $uri) {
            throw new InvalidArgumentException('A Gemini file requires both its resource name and URI.');
        }

        $this->uri = '' !== $uri ? $uri : null;

        if (null !== $purpose) {
            $purpose = trim($purpose);
        }

        if (null !== $expiresAt && !$expiresAt instanceof \DateTimeImmutable) {
            $expiresAt = \DateTimeImmutable::createFromTimestamp($expiresAt);
        }

        $this->expiresAt = $expiresAt;

        if (null !== $purpose) {
            $purpose = trim($purpose);
        }

        $this->purpose = '' !== $purpose ? $purpose : null;
    }
}
