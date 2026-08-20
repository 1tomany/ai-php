<?php

namespace OneToMany\AI\Resource\Query;

use OneToMany\AI\Model;

final readonly class QueryDefinition
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        public Model $model,
        public Prompt $prompt,
        public array $options = [],
    ) {
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getPrompt(): Prompt
    {
        return $this->prompt;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
