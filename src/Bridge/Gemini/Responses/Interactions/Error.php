<?php

namespace OneToMany\AI\Bridge\Gemini\Responses\Interactions;

final readonly class Error
{
    /**
     * @param ?non-empty-string $code
     * @param ?non-empty-string $message
     */
    public function __construct(
        public ?string $code = null,
        public ?string $message = null,
    ) {
    }
}
