<?php

namespace OneToMany\AI;

use OneToMany\AI\Exception\InvalidArgumentException;

use function array_last;
use function explode;
use function trim;
use function vsprintf;

final readonly class Model implements \Stringable
{
    public Vendor $vendor;

    /**
     * @var non-empty-string
     */
    public string $name;

    /**
     * @return non-empty-string
     */
    public string $label;

    /**
     * @throws InvalidArgumentException when the model name is empty
     */
    public function __construct(
        string|Vendor $vendor,
        string $name,
    ) {
        $this->vendor = Vendor::create($vendor);

        if ('' === $name = trim($name)) {
            throw new InvalidArgumentException('The model name cannot be empty.');
        }

        $this->name = $name;

        $this->label = vsprintf('%s:%s', [
            $this->vendor->value, $this->name,
        ]);
    }

    /**
     * @see \Stringable
     *
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->label;
    }

    public static function create(string|self $model): self
    {
        if ($model instanceof self) {
            return $model;
        }

        return new self(Vendor::fromModel($model), array_last(explode(':', $model, 2)));
    }

    public static function gemini(string $name): self
    {
        return new self(Vendor::Gemini, $name);
    }

    public static function openai(string $name): self
    {
        return new self(Vendor::OpenAI, $name);
    }

    public function getVendor(): Vendor
    {
        return $this->vendor;
    }

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return non-empty-string
     */
    public function getLabel(): string
    {
        return $this->label;
    }
}
