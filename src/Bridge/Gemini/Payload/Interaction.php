<?php

namespace OneToMany\AI\Bridge\Gemini\Payload;

use OneToMany\AI\Exception\UnexpectedValueException;

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
     */
    public function __construct(
        string $id,
        string $status,
        public array $steps = [],
        public ?Usage $usage = null,
        public array $errors = [],
    ) {
        if ('' === $id = trim($id)) {
            throw new UnexpectedValueException('The Gemini interaction is missing its ID.');
        }

        if ('' === $status = trim($status)) {
            throw new UnexpectedValueException('The Gemini interaction is missing its status.');
        }

        $this->id = $id;
        $this->status = $status;
    }
}
