<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses;

final readonly class ResponseOutputMessage
{
    /**
     * @param non-empty-string $type
     */
    public function __construct(
        public string $type,
        public ?string $text = null,
        public ?string $refusal = null,
    ) {
    }

    /**
     * @phpstan-assert-if-true 'output_text' $this->type
     */
    public function isOutputText(): bool
    {
        return 'output_text' === $this->type;
    }

    /**
     * @phpstan-assert-if-true 'refusal' $this->type
     */
    public function isRefusal(): bool
    {
        return 'refusal' === $this->type;
    }
}
