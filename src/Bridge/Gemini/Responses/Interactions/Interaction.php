<?php

namespace OneToMany\AI\Bridge\Gemini\Responses\Interactions;

use OneToMany\AI\Exception\InvalidArgumentException;

use function trim;

final readonly class Interaction
{
    /**
     * @param non-empty-string $id
     * @param non-empty-string $model
     * @param 'interaction' $object
     * @param non-empty-string $status
     * @param list<Step> $steps
     * @param list<Error> $errors
     */
    public function __construct(
        public \DateTimeImmutable $created,
        public string $id,
        public string $model,
        public string $object,
        public string $status,
        public array $steps = [],
        public \DateTimeImmutable $updated,
        public ?Usage $usage = null,
        public array $errors = [],
    ) {
        if ('' === $id = trim($id)) {
            throw new InvalidArgumentException('The interaction ID cannot be empty.');
        }

        $this->id = $id;

        if ('' === $status = trim($status)) {
            throw new InvalidArgumentException('The interaction status cannot be empty.');
        }

        $this->status = $status;
    }
}
