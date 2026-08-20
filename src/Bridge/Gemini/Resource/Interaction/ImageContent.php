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
        ?string $uri,
        string $mimeType,
    ) {
        parent::__construct('image', $uri, $mimeType);
    }
}
