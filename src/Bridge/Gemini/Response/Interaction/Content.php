<?php

namespace OneToMany\AI\Bridge\Gemini\Response\Interaction;

final readonly class Content
{
    /**
     * @param non-empty-string $type
     * @param ?non-empty-string $text
     */
    public function __construct(
        public string $type,
        public ?string $text = null,
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
