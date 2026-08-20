<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

final readonly class ImageContent extends FileContent
{
    /**
     * @see OneToMany\AI\Bridge\Gemini\Resource\Interaction\FileContent
     */
    public function __construct(
        string $mime_type,
        ?string $data = null,
        ?string $uri = null,
    ) {
        parent::__construct('image', $mime_type, $data, $uri);
    }
}
