<?php

namespace OneToMany\AI\Bridge\Gemini\Responses\Interactions;

final readonly class Usage
{
    /**
     * @param list<ModalityTokens> $cached_tokens_by_modality
     * @param list<GroundingToolCount> $grounding_tool_count
     * @param non-negative-int $total_tokens
     * @param non-negative-int $total_input_tokens
     * @param list<ModalityTokens> $input_tokens_by_modality
     * @param non-negative-int $total_cached_tokens
     * @param non-negative-int $total_output_tokens
     * @param non-negative-int $total_thought_tokens
     * @param non-negative-int $raw_prompt_token
     */
    public function __construct(
        public array $cached_tokens_by_modality = [],
        public array $grounding_tool_count = [],
        public int $total_tokens = 0,
        public int $total_input_tokens = 0,
        public array $input_tokens_by_modality = [],
        public int $total_cached_tokens = 0,
        public int $total_output_tokens = 0,
        public int $total_thought_tokens = 0,
        public int $raw_prompt_token = 0,
    ) {
    }
}
