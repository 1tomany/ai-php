<?php

namespace OneToMany\AI;

use OneToMany\AI\Contract\AiClientInterface;
use OneToMany\AI\Contract\Resource\FilesInterface;
use OneToMany\AI\Contract\Resource\QueriesInterface;

final readonly class AiClient implements AiClientInterface
{
    public function __construct(
        public FilesInterface $files,
        public QueriesInterface $queries,
    ) {
    }
}
