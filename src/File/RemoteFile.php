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

        $this->id = $id;

        if ('' === $mediaType = trim($mediaType)) {
            throw new InvalidArgumentException('The remote file media type cannot be empty.');
        }

        $this->mediaType = $mediaType;

        if ($provider->isGemini()) {
            if ('' === $uri = trim((string) $uri)) {
                throw new InvalidArgumentException('A Gemini file requires both its resource name and URI.');
            }

            $this->uri = $uri;
        } else {
            $this->uri = null;
        }

        if ('' !== $purpose = trim((string) $purpose)) {
            $this->purpose = $purpose;
        } else {
            $this->purpose = null;
        }
    }
}
