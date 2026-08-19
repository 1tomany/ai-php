<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses\Output;

final readonly class ResponseOutputText extends OutputMessage
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
