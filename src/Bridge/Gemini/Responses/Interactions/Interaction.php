<?php

namespace OneToMany\AI\Bridge\Gemini\Responses\Interactions;

final readonly class Interaction
{
    /**
     * @param non-empty-string $id
     * @param non-empty-string $status
     * @param list<Step> $steps
     * @param list<Error> $errors
     */
    public function __construct(
        public string $id,
        public string $status,
        public array $steps,
        public array $errors = [],
    ) {
    }
}
