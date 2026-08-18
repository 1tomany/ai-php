<?php

namespace OneToMany\AI\Bridge\Gemini\Payload;

final readonly class Step
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
