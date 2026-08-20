<?php

namespace OneToMany\AI\Bridge\OpenAI\Response\Response;

final readonly class ResponseError
{
    /**
     * @param non-empty-string $code
     * @param non-empty-string $message
     */
    public function __construct(
        public string $code,
        public string $message,
    ) {
    }
}
