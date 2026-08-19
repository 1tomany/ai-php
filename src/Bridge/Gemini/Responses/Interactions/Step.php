<?php

namespace OneToMany\AI\Bridge\Gemini\Responses\Interactions;

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
