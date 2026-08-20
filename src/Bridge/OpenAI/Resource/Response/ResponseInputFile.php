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
    public string $fileId;

    /**
     * @see OneToMany\AI\Bridge\OpenAI\Resource\Response\ResponseInput
     *
     * @throws InvalidArgumentException when the file ID is empty
     */
    public function __construct(?string $fileId)
    {
        parent::__construct('input_file');

        if ('' === $fileId = trim((string) $fileId)) {
            throw new InvalidArgumentException('The file ID cannot be empty.');
        }

        $this->fileId = $fileId;
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
            'file_id' => $this->fileId,
        ];
    }
}
