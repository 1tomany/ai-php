<?php

namespace OneToMany\AI\Inference;

use OneToMany\AI\Model;

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
