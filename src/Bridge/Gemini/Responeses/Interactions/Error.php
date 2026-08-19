<?php

namespace OneToMany\AI\Bridge\Gemini\Responeses\Interactions;

final readonly class Error
{
    public function __construct(
        public ?string $message = null,
    ) {
    }
}
