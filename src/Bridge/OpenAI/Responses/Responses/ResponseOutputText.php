<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses;

final readonly class ResponseOutputText
{
    /**
     * @param non-empty-string $text
     * @param 'output_text' $type
     */
    public function __construct(
        public string $text,
        public string $type,
    ) {
    }
}
