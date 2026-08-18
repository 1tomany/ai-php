<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

final readonly class InputTokenDetails
{
    /**
     * @param non-negative-int $cached_tokens
     */
    public function __construct(
        public int $cached_tokens = 0,
    ) {
    }
}
