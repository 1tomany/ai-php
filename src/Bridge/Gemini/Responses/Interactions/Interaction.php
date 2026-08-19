<?php

namespace OneToMany\AI\Bridge\Gemini\Responses\Interactions;

final readonly class Interaction
{
    /**
     * @param non-empty-string $id
     * @param non-empty-string $status
     * @param non-empty-string $model
     * @param 'interaction' $object
     * @param list<Step> $steps
     * @param list<Error> $errors
     */
    public function __construct(
        public string $id,
        public string $status,
        public ?Usage $usage,
        public \DateTimeImmutable $created,
        public string $model,
        public string $object,
        public array $steps,
        public \DateTimeImmutable $updated,
        public array $errors = [],
    ) {
    }
}
