<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses;

final readonly class ResponseOutputRefusal extends OutputMessage
{
    /**
     * @param non-empty-string $refusal
     * @param 'refusal' $type
     */
    public function __construct(
        public string $refusal,
        public string $type,
    ) {
    }
}
