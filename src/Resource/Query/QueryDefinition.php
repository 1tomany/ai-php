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
}
