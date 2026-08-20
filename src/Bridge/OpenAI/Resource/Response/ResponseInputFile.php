<?php

namespace OneToMany\AI\Bridge\OpenAI\Resource\Response;

use OneToMany\AI\Exception\InvalidArgumentException;

use function trim;

/**
 * @extends ResponseInput<'input_file'>
 */
final readonly class ResponseInputFile extends ResponseInput
{
    /**
     * @var non-empty-string
     */
    public string $file_id;

    /**
     * @see OneToMany\AI\Bridge\OpenAI\Resource\Response\ResponseInput
     *
     * @throws InvalidArgumentException when the file ID is empty
     */
    public function __construct(?string $file_id)
    {
        parent::__construct('input_file');

        if ('' === $file_id = trim((string) $file_id)) {
            throw new InvalidArgumentException('The file ID cannot be empty.');
        }

        $this->file_id = $file_id;
    }

    /**
     * @see \JsonSerializable
     *
     * @return array{
     *   type: 'input_file',
     *   file_id: non-empty-string,
     * }
     */
    #[\Override]
    public function jsonSerialize(): mixed
    {
        return [
            'type' => $this->type,
            'file_id' => $this->file_id,
        ];
    }
}
