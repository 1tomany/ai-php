<?php

namespace OneToMany\AI\Contract\Bridge;

use OneToMany\AI\Bridge\InferenceRequest;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\Inference\Response;

interface InferenceProviderInterface
{
    public function provider(): Provider;

    public function create(InferenceRequest $request): Response;
}
