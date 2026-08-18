<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

final readonly class Content
{
    public function __construct(
        public string $type,
        public ?string $text = null,
        public ?string $refusal = null,
    ) {
    }
}
