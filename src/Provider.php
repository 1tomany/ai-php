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

    public static function fromModel(string $model): self
    {
        $model = trim($model);

        if (!str_contains($model, ':')) {
            throw new InvalidArgumentException('A model string must use the "provider:model" format.');
        }

        [$provider] = explode(':', $model, 2);

        try {
            return self::from($provider);
        } catch (\ValueError $e) {
            throw new InvalidArgumentException(sprintf('The model provider "%s" is not supported.', $provider), previous: $e);
        }
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
