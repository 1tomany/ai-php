<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

final readonly class Output
{
    /**
     * @param list<Content> $content
     */
    public function __construct(
        public string $type,
        public array $content = [],
    ) {
    }
}
