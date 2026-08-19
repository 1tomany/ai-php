<?php

namespace OneToMany\AI\Bridge\Gemini\Responses\Interactions;

final readonly class Usage
{
    /**
     * @param non-negative-int $total_input_tokens
     * @param non-negative-int $total_output_tokens
     * @param non-negative-int $total_cached_tokens
     * @param non-negative-int $total_thought_tokens
     * @param non-negative-int $total_tokens
     */
    public function __construct(
        public int $total_input_tokens = 0,
        public int $total_output_tokens = 0,
        public int $total_cached_tokens = 0,
        public int $total_thought_tokens = 0,
        public int $total_tokens = 0,
    ) {
    }
}
