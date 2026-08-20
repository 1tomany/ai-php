<?php

namespace OneToMany\AI\Bridge\OpenAI\Resource\Response;

final readonly class EasyInputMessage implements \JsonSerializable
{
    /**
     * @var 'message'
     */
    public string $type;

    /**
     * @param 'assistant'|'developer'|'system'|'user' $role
     * @param list<ResponseInputFile|ResponseInputImage|ResponseInputText> $content
     */
    public function __construct(
        public string $role = 'user',
        public array $content = [],
    ) {
        $this->type = 'message';
    }

    /**
     * @see \JsonSerializable
     *
     * @return array{
     *   type: 'message',
     *   role: 'assistant'|'developer'|'system'|'user',
     *   content: list<ResponseInputFile|ResponseInputImage|ResponseInputText>,
     * }
     */
    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'role' => $this->role,
            'content' => $this->content,
        ];
    }
}
