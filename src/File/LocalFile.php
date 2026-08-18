<?php

namespace OneToMany\AI\File;

use OneToMany\AI\Exception\InvalidArgumentException;

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
     * @var positive-int
     */
    public int $size;

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

        if ('' === $mediaType = trim($mediaType)) {
            throw new InvalidArgumentException('The media type cannot be empty.');
        }

        if ('' === $name = trim($name ?? basename($path))) {
            throw new InvalidArgumentException('The file name cannot be empty.');
        }

        $size = @filesize($path);

        if (false === $size || $size < 1) {
            throw new InvalidArgumentException(sprintf('The file "%s" must not be empty.', $path));
        }

        $this->path = $path;
        $this->name = $name;
        $this->mediaType = $mediaType;
        $this->size = $size;
    }
}
