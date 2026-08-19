<?php

namespace OneToMany\AI\Example\Console;

use OneToMany\AI\AI;
use OneToMany\AI\Bridge\Gemini\FileProvider as GeminiFileProvider;
use OneToMany\AI\Bridge\Gemini\Normalizer\QueryRequestNormalizer as GeminiQueryRequestNormalizer;
use OneToMany\AI\Bridge\Gemini\QueryProvider as GeminiQueryProvider;
use OneToMany\AI\Bridge\OpenAI\FileProvider as OpenAIFileProvider;
use OneToMany\AI\Bridge\OpenAI\Normalizer\QueryRequestNormalizer as OpenAIQueryRequestNormalizer;
use OneToMany\AI\Bridge\OpenAI\QueryProvider as OpenAIQueryProvider;
use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\Files;
use OneToMany\AI\Resource\Queries;

final readonly class AIFactory
{
    public function __construct(
        private Transport $transport,
    ) {
    }

    public function create(
        Provider $provider,
        #[\SensitiveParameter]
        string $apiKey,
    ): AI {
        [$fileProvider, $queryProvider] = match ($provider) {
            Provider::Gemini => [
                new GeminiFileProvider($this->transport, $apiKey),
                new GeminiQueryProvider($this->transport, new GeminiQueryRequestNormalizer(), $apiKey),
            ],
            Provider::OpenAI => [
                new OpenAIFileProvider($this->transport, $apiKey),
                new OpenAIQueryProvider($this->transport, new OpenAIQueryRequestNormalizer(), $apiKey),
            ],
        };

        return new AI(
            new Files([$fileProvider]),
            new Queries([$queryProvider]),
        );
    }
}
