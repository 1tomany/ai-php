<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses;

final readonly class ResponseOutputItem
{
    /**
     * @param non-empty-string $id
     * @param non-empty-string $type
     * @param list<ResponseOutputMessage> $content
     */
    public function __construct(
        public string $id,
        public string $type,
        public array $content = [],
    ) {
    }
}
