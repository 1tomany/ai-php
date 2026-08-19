<?php

namespace OneToMany\AI\Contract\Resource;

use OneToMany\AI\Model;
use OneToMany\AI\Resource\Query\Prompt;
use OneToMany\AI\Resource\Query\Query;
use OneToMany\AI\Resource\Query\Response;

interface QueriesInterface
{
    /**
     * Provider-specific options are passed through verbatim. The model, input,
     * instructions, and schema from the typed arguments always take precedence.
     *
     * @param array<string, mixed> $options
     */
    public function compile(Model $model, Prompt $prompt, array $options = []): Query;

    public function run(Query $query): Response;

    /**
     * Provider-specific options are passed through verbatim. The model, input,
     * instructions, and schema from the typed arguments always take precedence.
     *
     * @param array<string, mixed> $options
     */
    public function compileAndRun(Model $model, Prompt $prompt, array $options = []): Response;
}
