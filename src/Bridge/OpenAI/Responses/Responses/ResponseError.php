<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses;

final readonly class ResponseError
{
    /**
     * @param non-empty-lowercase-string $code
     * @param non-empty-string $message
     */
    public function __construct(
        public string $code,
        public string $message,
    ) {
    }
}
