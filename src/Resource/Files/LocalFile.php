<?php

namespace OneToMany\AI\Resource\Files;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Exception\RuntimeException;

use function basename;
use function filesize;
use function is_file;
use function is_readable;
use function sprintf;
use function trim;

final readonly class LocalFile
{
    /**
     * @var non-empty-string
     */
    public string $path;

    /**
     * @var non-empty-string
     */
    public string $name;

    /**
     * @var non-empty-string
     */
    public string $mediaType;

    /**
     * @var non-negative-int
     */
    public int $size;

    /**
     * @throws InvalidArgumentException when the file path is empty
     * @throws InvalidArgumentException when the file is not readable
     * @throws InvalidArgumentException when the file name is empty
     * @throws InvalidArgumentException when the media type is empty
     * @throws RuntimeException when calculating the file size fails
     */
    public function __construct(
        string $path,
        string $mediaType,
        ?string $name = null,
    ) {
        if ('' === $path = trim($path)) {
            throw new InvalidArgumentException('The file path cannot be empty.');
        }

        if (!is_file($path) || !is_readable($path)) {
            throw new InvalidArgumentException(sprintf('The file "%s" is not readable.', $path));
        }

        $this->path = $path;

        if ('' === $name = trim($name ?? basename($path))) {
            throw new InvalidArgumentException('The file name cannot be empty.');
        }

        $this->name = $name;

        if ('' === $mediaType = trim($mediaType)) {
            throw new InvalidArgumentException('The media type cannot be empty.');
        }

        $this->mediaType = $mediaType;

        if (false === $size = @filesize($path)) {
            throw new RuntimeException(sprintf('Calculating the size of the file "%s" failed.', $path));
        }

        $this->size = $size;
    }
}
