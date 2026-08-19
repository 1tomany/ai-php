<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses\Output;

final readonly class Reasoning extends ResponseOutputItem
{
    /**
     * @param non-empty-string $id
     * @param 'reasoning' $type
     */
    public function __construct(
        public string $id,
        public string $type,
        public ?string $encrypted_content,
    ) {
    }
}
