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
     * @see OneToMany\AI\Provider::fromModel()
     * @see OneToMany\AI\Model::__construct()
     */
    public static function create(string $model): self
    {
        return new self(Provider::fromModel($model), array_last(explode(':', trim($model), 2)));
    }

    /**
     * @see OneToMany\AI\Model::__construct()
     */
    public static function gemini(string $name): self
    {
        return new self(Provider::Gemini, $name);
    }

    /**
     * @see OneToMany\AI\Model::__construct()
     */
    public static function openai(string $name): self
    {
        return new self(Provider::OpenAI, $name);
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
