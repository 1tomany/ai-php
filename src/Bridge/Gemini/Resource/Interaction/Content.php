<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

/**
 * @template TType of 'audio'|'document'|'image'|'text'|'video'
 */
abstract readonly class Content
{
    /**
     * @param TType $type
     */
    public function __construct(
        public string $type,
    ) {
    }
}
