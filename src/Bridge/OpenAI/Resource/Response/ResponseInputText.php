<?php

namespace OneToMany\AI\Bridge\OpenAI\Resource\Response;

use OneToMany\AI\Exception\InvalidArgumentException;

use function trim;

/**
 * @extends ResponseInput<'input_text'>
 */
final readonly class ResponseInputText extends ResponseInput
{
    /**
     * @var non-empty-string
     */
    public string $text;

    /**
     * @see OneToMany\AI\Bridge\OpenAI\Resource\Response\ResponseInput
     *
     * @throws InvalidArgumentException when the text is empty
     */
    public function __construct(?string $text)
    {
        parent::__construct('input_text');

        if ('' === $text = trim((string) $text)) {
            throw new InvalidArgumentException('The text cannot be empty.');
        }

        $this->text = $text;
    }

    /**
     * @see \JsonSerializable
     *
     * @return array{
     *   type: 'input_text',
     *   text: non-empty-string,
     * }
     */
    #[\Override]
    public function jsonSerialize(): mixed
    {
        return [
            'type' => $this->type,
            'text' => $this->text,
        ];
    }
}
