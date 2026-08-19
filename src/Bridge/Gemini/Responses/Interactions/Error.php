<?php

namespace OneToMany\AI\Bridge\Gemini\Responses\Interactions;

final readonly class Error
{
    public function __construct(
        public ?string $message = null,
    ) {
    }
}
