<?php

namespace OneToMany\AI\Contract\Resource;

use OneToMany\AI\Model;
use OneToMany\AI\Resource\Inference\Prompt;
use OneToMany\AI\Resource\Inference\Response;

interface InferenceInterface
{
    /**
     * Provider-specific options are passed through verbatim. The model, input,
     * instructions, and schema from the typed arguments always take precedence.
     *
     * @param array<string, mixed> $options
     */
    public function create(Model $model, Prompt $prompt, array $options = []): Response;
}
