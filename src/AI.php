<?php

namespace OneToMany\AI;

use OneToMany\AI\File\FilesInterface;
use OneToMany\AI\Inference\InferenceInterface;

final readonly class AI
{
    public function __construct(
        public FilesInterface $files,
        public InferenceInterface $inference,
    ) {
    }
}
