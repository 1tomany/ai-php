<?php

namespace OneToMany\AI\Resource\File;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Exception\RuntimeException;

use function basename;
use function filesize;
use function is_file;
use function is_readable;
use function mime_content_type;
use function sprintf;
use function strtolower;
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
     * @var non-empty-lowercase-string
     */
    public string $mimeType;

    /**
     * @var non-negative-int
     */
    public int $size;

    /**
     * @throws InvalidArgumentException when the file path is empty
     * @throws InvalidArgumentException when the file is not readable
     * @throws InvalidArgumentException when the file name is empty
     * @throws InvalidArgumentException when the MIME type is empty
     * @throws RuntimeException when calculating the file size fails
     */
    public function __construct(
        string $path,
        ?string $mimeType = null,
        ?string $name = null,
    ) {
        if ('' === $path = trim($path)) {
            throw new InvalidArgumentException('The file path cannot be empty.');
        }

        if (!is_file($path) || !is_readable($path)) {
            throw new InvalidArgumentException(sprintf('The file "%s" is not readable.', $path));
        }

        $this->path = $path;

        if ('' === $mimeType = trim((string) $mimeType)) {
            $mimeType = @mime_content_type(filename: $path);
        }

        if ('' === $name = trim($name ?? basename($path))) {
            throw new InvalidArgumentException('The file name cannot be empty.');
        }

        $this->name = $name;

        if (false === $mimeType || '' === $mimeType) {
            throw new InvalidArgumentException('The MIME type cannot be empty.');
        }

        $this->mimeType = strtolower($mimeType);

        if (false === $size = @filesize($path)) {
            throw new RuntimeException(sprintf('Calculating the size of the file "%s" failed.', $path));
        }

        $this->size = $size;
    }

    /**
     * @return non-empty-string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return non-empty-lowercase-string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @return non-negative-int
     */
    public function getSize(): int
    {
        return $this->size;
    }
}
