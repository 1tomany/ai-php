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
        string $mime_type,
        ?string $data = null,
        ?string $uri = null,
    ) {
        parent::__construct('audio', $mime_type, $data, $uri);
    }
}
