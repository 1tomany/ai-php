<?php

namespace OneToMany\AI;

use OneToMany\AI\Exception\InvalidArgumentException;

use function explode;
use function sprintf;
use function str_contains;
use function trim;

enum Vendor: string
{
    case Gemini = 'gemini';
    case OpenAI = 'openai';

    /**
     * @throws InvalidArgumentException when the vendor is not valid
     */
    public static function create(string|self $vendor): self
    {
        if (!$vendor instanceof self) {
            try {
                return self::from($vendor);
            } catch (\ValueError $e) {
                throw new InvalidArgumentException(sprintf('The vendor "%s" is not valid.', $vendor), previous: $e);
            }
        }

        return $vendor;
    }

    /**
     * @throws InvalidArgumentException when the model format is invalid
     * @throws InvalidArgumentException when the vendor is not found
     */
    public static function fromModel(string $model): self
    {
        $model = trim($model);

        if (!str_contains($model, ':')) {
            throw new InvalidArgumentException('The model must use the "vendor:model" format.');
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
