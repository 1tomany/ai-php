<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

/**
 * @template TType of 'audio'|'image'|'text'|'video'
 */
abstract readonly class ResponseFormat
{
    /**
     * @param TType $type
     */
    public function __construct(
        public string $type,
    ) {
    }

    /**
     * @phpstan-assert-if-true 'text' $this->type
     */
    public function isTypeText(): bool
    {
        return 'text' === $this->type;
    }
}
