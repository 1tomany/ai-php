<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

final readonly class AudioContent extends FileContent
{
    /**
     * @see OneToMany\AI\Bridge\Gemini\Resource\Interaction\Content
     *
     * @param ?non-empty-string $data
     * @param ?non-empty-string $uri
     */
    public function __construct(
        ?string $data,
        ?string $uri,
    ) {
        parent::__construct('audio', $data, $uri);
    }
}
