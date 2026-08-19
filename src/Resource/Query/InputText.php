<?php

namespace OneToMany\AI\Resource\Query;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Exception\RuntimeException;

use function file_exists;
use function file_get_contents;
use function is_file;
use function is_readable;
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
     * @throws InvalidArgumentException when the file does not exist
     * @throws InvalidArgumentException when the path is not a file
     * @throws InvalidArgumentException when the file is not readable
     * @throws RuntimeException when reading the file fails
     * @throws InvalidArgumentException when the file is empty
     */
    public static function fromFile(string $path): self
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException(sprintf('The input text file "%s" does not exist.', $path));
        }

        if (!is_file($path)) {
            throw new InvalidArgumentException(sprintf('The input text path "%s" is not a file.', $path));
        }

        if (!is_readable($path)) {
            throw new InvalidArgumentException(sprintf('The input text file "%s" is not readable.', $path));
        }

        if (false === $text = @file_get_contents($path)) {
            throw new RuntimeException(sprintf('Reading the input text file "%s" failed.', $path));
        }

        return new self($text);
    }

    /**
     * @see \Stringable::__toString()
     */
    #[\Override]
    public function __toString(): string
    {
        return $this->text;
    }
}
