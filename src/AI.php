<?php

namespace OneToMany\AI;

use OneToMany\AI\Contract\Resource\FilesInterface;
use OneToMany\AI\Contract\Resource\InferenceInterface;

final readonly class AI
{
    public function __construct(
        public FilesInterface $files,
        public InferenceInterface $inference,
    ) {
    }
}
