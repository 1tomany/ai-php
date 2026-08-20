<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

/**
 * @extends FileContent<'image'>
 */
final readonly class ImageContent extends FileContent
{
    /**
     * @see OneToMany\AI\Bridge\Gemini\Resource\Interaction\FileContent
     */
    public function __construct(
        string $mime_type,
        ?string $uri = null,
        ?string $data = null,
    ) {
        parent::__construct('image', $mime_type, $uri, $data);
    }
}
