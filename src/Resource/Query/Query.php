<?php

namespace OneToMany\AI\Resource\Query;

use OneToMany\AI\Model;

final readonly class Query
{
    /**
     * @param array<string, mixed> $request
     */
    public function __construct(
        public Model $model,
        public array $request,
    ) {
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRequest(): array
    {
        return $this->request;
    }
}
