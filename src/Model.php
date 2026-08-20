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
        public Vendor $vendor,
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
        return sprintf('%s:%s', $this->vendor->getValue(), $this->name);
    }

    public static function create(string|self $model): self
    {
        if ($model instanceof self) {
            return $model;
        }

        return new self(Vendor::fromModel($model), array_last(explode(':', trim($model), 2)));
    }

    public static function gemini(string $name): self
    {
        return new self(Vendor::Gemini, $name);
    }

    public static function openai(string $name): self
    {
        return new self(Vendor::OpenAI, $name);
    }
}
