<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses;

final readonly class InputTokenDetails
{
    /**
     * @param non-negative-int $cache_write_tokens
     * @param non-negative-int $cached_tokens
     */
    public function __construct(
        public int $cache_write_tokens = 0,
        public int $cached_tokens = 0,
    ) {
    }
}
