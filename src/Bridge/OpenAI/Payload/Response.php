<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

use OneToMany\AI\Exception\InvalidArgumentException;

use function trim;

final readonly class Response
{
    /**
     * @var non-empty-string
     */
    public string $id;

    /**
     * @var non-empty-string
     */
    public string $status;

    /**
     * @param list<Output> $output
     *
     * @throws InvalidArgumentException when the response ID is empty
     * @throws InvalidArgumentException when the response status is empty
     */
    public function __construct(
        string $id,
        string $status,
        public array $output = [],
        public ?Usage $usage = null,
        public ?Error $error = null,
        public ?IncompleteDetails $incomplete_details = null,
    ) {
        if ('' === $id = trim($id)) {
            throw new InvalidArgumentException('The response ID cannot be empty.');
        }

        if ('' === $status = trim($status)) {
            throw new InvalidArgumentException('The response status cannot be empty.');
        }

        $this->id = $id;
        $this->status = $status;
    }
}
