<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses;

use OneToMany\AI\Bridge\OpenAI\Payload\Usage;

final readonly class Response
{
    /**
     * @param non-empty-string $id
     * @param 'response' $object
     * @param ?positive-int $completed_at
     * @param ?non-negative-int $max_output_tokens
     * @param non-empty-lowercase-string $model
     * @param list<Output> $output
     * @param ?non-empty-string $previous_response_id
     * @param ?non-empty-string $prompt_cache_key
     * @param ?non-empty-string $safety_identifier
     * @param non-negative-int $top_logprobs
     */
    public function __construct(
        public string $id,
        public string $object,
        public int $created_at,
        public Status $status,
        public bool $background,
        public ?Billing $billing,
        public ?int $completed_at,
        public ?Error $error,
        public float $frequency_penalty,
        public ?IncompleteDetails $incomplete_details,
        public ?int $max_output_tokens,
        public string $model,
        public array $output,
        public bool $parallel_tool_calls,
        public float $presence_penalty,
        public ?string $previous_response_id,
        public ?string $prompt_cache_key,
        public ?string $safety_identifier,
        public ServiceTier $service_tier,
        public bool $store,
        public float $temperature,
        public int $top_logprobs,
        public float $top_p,
        public Truncation $truncation,
        public Usage $usage,
    ) {
    }

    /**
     * @return ?non-empty-string
     */
    public function getText(): ?string
    {
        foreach ($this->output as $item) {
            if (!empty($item->getText())) {
                return $item->getText();
            }
        }

        return null;
    }

    /**
     * @return ?non-empty-string
     */
    public function getError(): ?string
    {
        if ($error = $this->error) {
            return $error->message;
        }

        foreach ($this->output as $item) {
            if (!empty($item->getRefusal())) {
                return $item->getRefusal();
            }
        }

        return null;
    }
}
