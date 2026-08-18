<?php

namespace OneToMany\AI\File;

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

    /**
     * @var ?non-empty-string
     */
    public ?string $purpose;

    public function __construct(
        public Provider $provider,
        string $id,
        string $mediaType,
        ?string $uri = null,
        public ?\DateTimeImmutable $expiresAt = null,
        ?string $purpose = null,
    ) {
        if ('' === $id = trim($id)) {
            throw new InvalidArgumentException('The remote file ID cannot be empty.');
        }

        if ('' === $mediaType = trim($mediaType)) {
            throw new InvalidArgumentException('The remote file media type cannot be empty.');
        }

        $uri = null !== $uri ? trim($uri) : null;
        $purpose = null !== $purpose ? trim($purpose) : null;

        if (Provider::Gemini === $provider && empty($uri)) {
            throw new InvalidArgumentException('A Gemini file requires both its resource name and URI.');
        }

        $this->id = $id;
        $this->mediaType = $mediaType;
        $this->uri = '' !== $uri ? $uri : null;
        $this->purpose = '' !== $purpose ? $purpose : null;
    }
}
