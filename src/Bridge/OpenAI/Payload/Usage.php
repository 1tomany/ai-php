<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

final readonly class Usage
{
    /**
     * @param non-negative-int $input_tokens
     * @param non-negative-int $output_tokens
     * @param non-negative-int $total_tokens
     */
    public function __construct(
        public int $input_tokens = 0,
        public int $output_tokens = 0,
        public int $total_tokens = 0,
        public InputTokenDetails $input_token_details = new InputTokenDetails(),
        public OutputTokenDetails $output_token_details = new OutputTokenDetails(),
    ) {
    }
}
