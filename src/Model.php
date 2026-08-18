<?php

namespace OneToMany\AI;

use OneToMany\AI\Exception\InvalidArgumentException;

use function array_last;
use function explode;
use function sprintf;
use function trim;

final readonly class Model implements \Stringable
{
    /**
     * @var non-empty-string
     */
    public string $name;

    /**
     * @throws InvalidArgumentException when the model name is empty
     */
    public function __construct(
        public Provider $provider,
        string $name,
    ) {
        if ('' === $name = trim($name)) {
            throw new InvalidArgumentException('The model name cannot be empty.');
        }

        $this->name = $name;
    }

    /**
     * @see \Stringable
     *
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return sprintf('%s:%s', $this->provider->getValue(), $this->name);
    }

    /**
     * @throws InvalidArgumentException when the model format is invalid
     * @throws InvalidArgumentException when the provider is not found
     * @throws InvalidArgumentException when the model name is empty
     */
    public static function create(string $model): self
    {
        $name = array_last(explode(':', trim($model), 2));

        return new self(Provider::fromModel($model), $name);
    }

    /**
     * @throws InvalidArgumentException when the model name is empty
     */
    public static function gemini(string $name): self
    {
        return new self(Provider::Gemini, $name);
    }

    /**
     * @throws InvalidArgumentException when the model name is empty
     */
    public static function openai(string $name): self
    {
        return new self(Provider::OpenAI, $name);
    }
}
