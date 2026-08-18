<?php

namespace OneToMany\AI\Bridge\Gemini\Payload;

final readonly class Content
{
    public function __construct(
        public string $type,
        public ?string $text = null,
    ) {
    }
}
