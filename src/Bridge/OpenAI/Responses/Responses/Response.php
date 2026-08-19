<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses;

use OneToMany\AI\Bridge\OpenAI\Responses\Responses\Enum\ResponseStatus;
use OneToMany\AI\Bridge\OpenAI\Responses\Responses\Enum\ServiceTier;
use OneToMany\AI\Bridge\OpenAI\Responses\Responses\Enum\Truncation;

final readonly class Response
{
    /**
     * @param non-empty-string $id
     * @param 'response' $object
     * @param positive-int $created_at
     * @param non-empty-lowercase-string $model
     * @param list<ResponseOutputMessage|Reasoning> $output
     * @param ?positive-int $completed_at
     * @param ?non-negative-int $max_output_tokens
     * @param ?non-negative-int $max_tool_calls
     * @param ?non-empty-string $previous_response_id
     * @param ?non-empty-string $safety_identifier
     * @param ?int<0, 20> $top_logprobs
     */
    public function __construct(
        public string $id,
        public int $created_at,
        public ?ResponseError $error,
        public ?IncompleteDetails $incomplete_details,
        // public ?string $instructions,
        // public ?Metadata $metadata,
        public string $model,
        public string $object,
        public array $output,
        public bool $parallel_tool_calls,
        public ?float $temperature,
        // public ?ToolChoice $tool_choice,
        // public array $tools,
        public ?float $top_p,
        public ?bool $background,
        public ?int $completed_at,
        // public ?Conversation $conversation,
        public ?int $max_output_tokens,
        public ?int $max_tool_calls,
        // public ?Moderation $moderation,
        public ?string $previous_response_id,
        // public ?Prompt $prompt,
        // public ?string $prompt_cache_key,
        // public ?PromptCacheOptions $prompt_cache_options,
        // public ?Reasoning $reasoning,
        public ?string $safety_identifier,
        public ?ServiceTier $service_tier,
        public ?ResponseStatus $status,
        // public ?ResponseTextConfig $text,
        public ?int $top_logprobs,
        public ?Truncation $truncation,
        public ?ResponseUsage $usage,
    ) {
    }

    /**
     * @return ?non-empty-string
     */
    public function getText(): ?string
    {
        foreach ($this->output as $output) {
            if ($output instanceof ResponseOutputMessage) {
                if (null !== $output->getText()) {
                    return $output->getText();
                }
            }
        }

        return null;
    }

    /**
     * @return ?non-empty-string
     */
    public function getError(): ?string
    {
        if ($this->error instanceof ResponseError) {
            return $this->error->message;
        }

        foreach ($this->output as $output) {
            if ($output instanceof ResponseOutputMessage) {
                if (null !== $output->getRefusal()) {
                    return $output->getRefusal();
                }
            }
        }

        return null;
    }
}
