<?php

namespace OneToMany\AI\Contract\Bridge;

use OneToMany\AI\Model;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\Queries\Prompt;
use OneToMany\AI\Resource\Queries\Query;
use OneToMany\AI\Resource\Queries\Response;

interface QueryProviderInterface
{
    public function provider(): Provider;

    /**
     * @param array<string, mixed> $options
     */
    public function compile(Model $model, Prompt $prompt, array $options = []): Query;

    public function run(Query $query): Response;
}
