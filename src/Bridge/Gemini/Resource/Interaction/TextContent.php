<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

use function assert;

final readonly class TextContent extends Content implements \JsonSerializable
{
    /**
     * @see OneToMany\AI\Bridge\Gemini\Resource\Interaction\Content
     *
     * @param non-empty-string $text
     */
    public function __construct(
        public string $text,
    ) {
        parent::__construct('text');

        assert(true === $this->isTypeText());
    }

    /**
     * @see \JsonSerializable
     *
     * @return array{
     *   type: 'text',
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
