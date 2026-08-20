<?php

namespace OneToMany\AI\Bridge\OpenAI\Resource\Response;

use function strtolower;

final class EasyInputMessage implements \JsonSerializable
{
    /**
     * @var 'assistant'|'developer'|'system'|'user'
     */
    private string $role = 'user';

    /**
     * @var 'message'
     */
    private string $type = 'message';

    /**
     * @var list<ResponseInputFile|ResponseInputImage|ResponseInputText>
     */
    private array $content = [];

    /**
     * @param 'assistant'|'developer'|'system'|'user' $role
     */
    public function __construct(
        string $role = 'user',
    ) {
        $this->role = strtolower($role);
    }

    public function addContent(ResponseInputFile|ResponseInputImage|ResponseInputText $input): static
    {
        $this->content[] = $input;

        return $this;
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
