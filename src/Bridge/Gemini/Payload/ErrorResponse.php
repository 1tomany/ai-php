<?php

namespace OneToMany\AI\Bridge\Gemini\Payload;

final readonly class ErrorResponse
{
    public function __construct(
        public ?Error $error = null,
    ) {
    }
}
