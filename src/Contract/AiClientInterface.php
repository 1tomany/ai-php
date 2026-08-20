<?php

namespace OneToMany\AI\Contract;

use OneToMany\AI\Contract\Resource\FilesInterface;
use OneToMany\AI\Contract\Resource\QueriesInterface;

interface AiClientInterface
{
    public FilesInterface $files { get; }

    public QueriesInterface $queries { get; }
}
