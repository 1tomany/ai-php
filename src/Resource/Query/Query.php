<?php

namespace OneToMany\AI\Resource\Query;

use OneToMany\AI\Model;
use OneToMany\AI\Provider;

final readonly class Query
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public Model $model,
        public array $payload,
    ) {
    }

    public function getProvider(): Provider
    {
        return $this->model->provider;
    }
}
