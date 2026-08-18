<?php

namespace OneToMany\AI\Bridge;

use OneToMany\AI\Inference\Response;
use OneToMany\AI\Provider;

interface InferenceProviderInterface
{
    public function provider(): Provider;

    public function create(InferenceRequest $request): Response;
}
