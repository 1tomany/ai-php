<?php

namespace OneToMany\AI;

use OneToMany\AI\Exception\InvalidArgumentException;

use function explode;
use function sprintf;
use function str_contains;
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

    public static function gemini(string $name): self
    {
        return new self(Provider::Gemini, $name);
    }

    public static function openAI(string $name): self
    {
        return new self(Provider::OpenAI, $name);
    }

    public static function fromString(string $model): self
    {
        $model = trim($model);

        if (!str_contains($model, ':')) {
            throw new InvalidArgumentException('A model string must use the "provider:model" format.');
        }

        [$provider, $name] = explode(':', $model, 2);

        try {
            return new self(Provider::from(trim($provider)), $name);
        } catch (\ValueError $e) {
            throw new InvalidArgumentException(sprintf('The model provider "%s" is not supported.', $provider), previous: $e);
        }
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return sprintf('%s:%s', $this->provider->getValue(), $this->name);
    }
}
