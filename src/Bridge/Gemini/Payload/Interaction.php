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
     *
     * @throws UnexpectedValueException when the interaction ID is missing
     * @throws UnexpectedValueException when the interaction status is missing
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

        $this->id = $id;

        if ('' === $status = trim($status)) {
            throw new UnexpectedValueException('The Gemini interaction is missing its status.');
        }

        $this->status = $status;
    }
}
