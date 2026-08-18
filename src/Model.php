<?php

namespace OneToMany\AI;

use OneToMany\AI\Exception\InvalidArgumentException;

use function explode;
use function sprintf;
use function trim;

final readonly class Model implements \Stringable
{
    /**
     * @var non-empty-string
     */
    public string $name;

    public function __construct(
        public Provider $provider,
        string $name,
    ) {
        if ('' === $name = trim($name)) {
            throw new InvalidArgumentException('The model name cannot be empty.');
        }

        $this->name = $name;
    }

    public static function create(string $model): self
    {
        $provider = Provider::fromModel($model);
        [, $name] = explode(':', trim($model), 2);

        return new self($provider, $name);
    }

    public static function gemini(string $name): self
    {
        return new self(Provider::Gemini, $name);
    }

    public static function openai(string $name): self
    {
        return new self(Provider::OpenAI, $name);
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return sprintf('%s:%s', $this->provider->getValue(), $this->name);
    }
}
