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
        ?string $uri = null,
        ?string $data = null,
    ) {
        parent::__construct('audio', $mime_type, $uri, $data);
    }
}
