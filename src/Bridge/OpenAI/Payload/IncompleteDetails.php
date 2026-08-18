<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

final readonly class IncompleteDetails
{
    public function __construct(
        public ?string $reason = null,
    ) {
    }
}
