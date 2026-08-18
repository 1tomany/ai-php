<?php

namespace OneToMany\AI\Bridge\Gemini\Payload;

final readonly class Error
{
    public function __construct(
        public ?string $message = null,
    ) {
    }
}
