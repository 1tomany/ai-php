<?php

namespace OneToMany\AI\Bridge;

use OneToMany\AI\Model;
use OneToMany\AI\Resource\Query\Prompt;

final readonly class QueryRequest
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
