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
}
