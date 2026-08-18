<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

use OneToMany\AI\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Attribute\SerializedName;

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
     * @throws UnexpectedValueException when the response ID is missing
     * @throws UnexpectedValueException when the response status is missing
     */
    public function __construct(
        string $id,
        string $status,
        public array $output = [],
        public ?Usage $usage = null,
        public ?Error $error = null,
        #[SerializedName('incomplete_details')]
        public ?IncompleteDetails $incompleteDetails = null,
    ) {
        if ('' === $id = trim($id)) {
            throw new UnexpectedValueException('The OpenAI response is missing its ID.');
        }

        if ('' === $status = trim($status)) {
            throw new UnexpectedValueException('The OpenAI response is missing its status.');
        }

        $this->id = $id;
        $this->status = $status;
    }
}
