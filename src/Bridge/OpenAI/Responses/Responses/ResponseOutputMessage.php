<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses;

use OneToMany\AI\Bridge\OpenAI\Responses\Responses\Enum\ResponseOutputMessageStatus;
use OneToMany\AI\Exception\InvalidArgumentException;

use function sprintf;

final readonly class ResponseOutputMessage
{
    /**
     * @param non-empty-string $id
     * @param list<ResponseOutputText|ResponseOutputRefusal> $content
     * @param 'assistant' $role
     */
    public function __construct(
        public string $id,
        public array $content,
        public ResponseOutputMessageStatus $status,
        public string $type,
        public ?string $encrypted_content,
        public ?string $phase,
        public string $role,
    ) {
        // if ($this->type->isMessage() && empty($this->content)) {
        //     throw new InvalidArgumentException(sprintf('The content cannot be empty when the type is "%s".', $this->type->value));
        // }
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
