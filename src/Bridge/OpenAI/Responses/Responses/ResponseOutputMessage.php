<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses;

use OneToMany\AI\Bridge\OpenAI\Responses\Responses\Enum\ResponseOutputMessageStatus;

final readonly class ResponseOutputMessage extends ResponseOutputItem
{
    /**
     * @param non-empty-string $id
     * @param list<OutputMessage> $content
     * @param 'assistant' $role
     * @param 'message' $type
     */
    public function __construct(
        public string $id,
        public array $content,
        public string $role,
        public ResponseOutputMessageStatus $status,
        public string $type,
        public ?string $phase,
    ) {
    }

    /**
     * @return ?non-empty-string
     */
    public function getText(): ?string
    {
        foreach ($this->content as $content) {
            if ($content instanceof ResponseOutputText) {
                return $content->text;
            }
        }

        return null;
    }

    /**
     * @return ?non-empty-string
     */
    public function getRefusal(): ?string
    {
        foreach ($this->content as $content) {
            if ($content instanceof ResponseOutputRefusal) {
                return $content->refusal;
            }
        }

        return null;
    }
}
