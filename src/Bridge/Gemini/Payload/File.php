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

    /**
     * @throws UnexpectedValueException when the file name is missing
     * @throws UnexpectedValueException when the file URI is missing
     * @throws UnexpectedValueException when the media type is missing
     */
    public function __construct(
        string $name,
        string $uri,
        string $mimeType,
        public ?string $expirationTime = null,
    ) {
        if ('' === $name = trim($name)) {
            throw new UnexpectedValueException('The Gemini file response is missing its name.');
        }

        $this->name = $name;

        if ('' === $uri = trim($uri)) {
            throw new UnexpectedValueException('The Gemini file response is missing its URI.');
        }

        $this->uri = $uri;

        if ('' === $mimeType = trim($mimeType)) {
            throw new UnexpectedValueException('The Gemini file response is missing its media type.');
        }

        $this->mimeType = $mimeType;
    }
}
