<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses;

final readonly class IncompleteDetails
{
    public function __construct(
        public string $reason,
    ) {
    }
}
