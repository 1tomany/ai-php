<?php

namespace OneToMany\AI\Bridge\Gemini\Responses\Interactions;

final readonly class Content
{
    public function __construct(
        public string $type,
        public ?string $text = null,
    ) {
    }
}
