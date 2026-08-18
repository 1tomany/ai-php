<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

final readonly class Error
{
    public function __construct(
        public ?string $message = null,
    ) {
    }
}
