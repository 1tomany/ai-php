<?php

namespace OneToMany\AI;

use OneToMany\AI\Contract\Resource\FilesInterface;
use OneToMany\AI\Contract\Resource\QueriesInterface;

final readonly class AI
{
    public function __construct(
        public FilesInterface $files,
        public QueriesInterface $queries,
    ) {
    }
}
