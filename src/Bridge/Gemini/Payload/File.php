<?php

namespace OneToMany\AI\Bridge\Gemini\Payload;

use OneToMany\AI\Exception\InvalidArgumentException;

use function trim;

final readonly class File
{
    /** @var non-empty-string */
    public string $name;

    /** @var non-empty-string */
    public string $uri;

    /** @var non-empty-string */
    public string $mimeType;

    /**
     * @throws InvalidArgumentException when the file name is missing
     * @throws InvalidArgumentException when the file URI is missing
     * @throws InvalidArgumentException when the media type is missing
     */
    public function __construct(
        string $name,
        string $uri,
        string $mimeType,
        public ?string $expirationTime = null,
    ) {
        if ('' === $name = trim($name)) {
            throw new InvalidArgumentException('The file name cannot be empty.');
        }

        $this->name = $name;

        if ('' === $uri = trim($uri)) {
            throw new InvalidArgumentException('The file URI cannot be empty.');
        }

        $this->uri = $uri;

        if ('' === $mimeType = trim($mimeType)) {
            throw new InvalidArgumentException('The MIME type cannot be empty.');
        }

        $this->mimeType = $mimeType;
    }
}
