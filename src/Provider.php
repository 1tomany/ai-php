<?php

namespace OneToMany\AI;

use OneToMany\AI\Exception\InvalidArgumentException;

use function explode;
use function sprintf;
use function str_contains;
use function trim;

enum Provider: string
{
    case Gemini = 'gemini';
    case OpenAI = 'openai';

    /**
     * @throws InvalidArgumentException when the provider is not valid
     */
    public static function create(string|self $provider): self
    {
        if ($provider instanceof self) {
            return $provider;
        }

        try {
            return self::from($provider);
        } catch (\ValueError $e) {
            throw new InvalidArgumentException(sprintf('The provider "%s" is not valid.', $provider), previous: $e);
        }
    }

    /**
     * @throws InvalidArgumentException when the model format is invalid
     * @throws InvalidArgumentException when the provider is not found
     */
    public static function fromModel(string $model): self
    {
        $model = trim($model);

        if (!str_contains($model, ':')) {
            throw new InvalidArgumentException('The model must use the "provider:model" format.');
        }

        return self::create(explode(':', $model)[0]);
    }

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return non-empty-lowercase-string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @phpstan-assert-if-true self::Gemini $this
     */
    public function isGemini(): bool
    {
        return self::Gemini === $this;
    }

    /**
     * @phpstan-assert-if-true self::OpenAI $this
     */
    public function isOpenAI(): bool
    {
        return self::OpenAI === $this;
    }
}
