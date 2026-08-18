<?php

namespace OneToMany\AI\Bridge\Gemini\Payload;

use OneToMany\AI\Exception\InvalidArgumentException;

use function trim;

final readonly class Interaction
{
    /** @var non-empty-string */
    public string $id;

    /** @var non-empty-string */
    public string $status;

    /**
     * @param list<Step> $steps
     * @param list<Error> $errors
     *
     * @throws InvalidArgumentException when the interaction ID is empty
     * @throws InvalidArgumentException when the interaction status is empty
     */
    public function __construct(
        string $id,
        string $status,
        public array $steps = [],
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
