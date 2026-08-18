<?php

namespace OneToMany\AI\Bridge;

use OneToMany\AI\Inference\Prompt;
use OneToMany\AI\Model;

final readonly class InferenceRequest
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        public Model $model,
        public Prompt $prompt,
        public array $options = [],
    ) {
    }
}
