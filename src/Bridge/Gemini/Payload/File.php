<?php

namespace OneToMany\AI\Bridge\Gemini\Payload;

use OneToMany\AI\Exception\UnexpectedValueException;

use function trim;

final readonly class File
{
    /** @var non-empty-string */
    public string $name;

    /** @var non-empty-string */
    public string $uri;

    /** @var non-empty-string */
    public string $mimeType;

    public function __construct(
        string $name,
        string $uri,
        string $mimeType,
        public ?string $expirationTime = null,
    ) {
        if ('' === $name = trim($name)) {
            throw new UnexpectedValueException('The Gemini file response is missing its name.');
        }

        if ('' === $uri = trim($uri)) {
            throw new UnexpectedValueException('The Gemini file response is missing its URI.');
        }

        if ('' === $mimeType = trim($mimeType)) {
            throw new UnexpectedValueException('The Gemini file response is missing its media type.');
        }

        $this->name = $name;
        $this->uri = $uri;
        $this->mimeType = $mimeType;
    }
}
