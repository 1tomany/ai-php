<?php

namespace OneToMany\AI\Resource\Query;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Exception\RuntimeException;

use function file_get_contents;
use function sprintf;
use function trim;

final readonly class InputText implements \Stringable
{
    /**
     * @var non-empty-string
     */
    public string $text;

    /**
     * @throws InvalidArgumentException when the input text is empty
     */
    public function __construct(?string $text)
    {
        if ('' === $text = trim((string) $text)) {
            throw new InvalidArgumentException('The input text cannot be empty.');
        }

        $this->text = $text;
    }

    /**
     * @see \Stringable
     *
     * @return non-empty-string
     */
    #[\Override]
    public function __toString(): string
    {
        return $this->getText();
    }

    /**
     * @see OneToMany\AI\Resource\Query\InputText::__construct()
     *
     * @throws RuntimeException when reading the file fails
     */
    public static function fromFile(string $path): self
    {
        if (false === $text = @file_get_contents($path)) {
            throw new RuntimeException(sprintf('Reading the input text file "%s" failed.', $path));
        }

        return new self($text);
    }

    /**
     * @return non-empty-string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
