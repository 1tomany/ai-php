<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

final readonly class ErrorResponse
{
    public function __construct(
        public ?Error $error = null,
    ) {
    }
}
