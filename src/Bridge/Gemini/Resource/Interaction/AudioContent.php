<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

/**
 * @extends FileContent<'audio'>
 */
final readonly class AudioContent extends FileContent
{
    /**
     * @see OneToMany\AI\Bridge\Gemini\Resource\Interaction\FileContent
     */
    public function __construct(
        ?string $uri,
        string $mimeType,
    ) {
        parent::__construct('audio', $uri, $mimeType);
    }
}
