<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

final readonly class OutputTokenDetails
{
    /**
     * @param non-negative-int $reasoning_tokens
     */
    public function __construct(
        public int $reasoning_tokens = 0,
    ) {
    }
}
