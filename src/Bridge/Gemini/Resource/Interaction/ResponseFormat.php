<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

abstract readonly class ResponseFormat
{
    /**
     * @param 'audio'|'image'|'text'|'video' $type
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
