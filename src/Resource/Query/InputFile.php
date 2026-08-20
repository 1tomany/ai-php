<?php

namespace OneToMany\AI\Resource\Query;

use OneToMany\AI\Exception\InvalidArgumentException;

use function trim;

final readonly class InputFile
{
    /**
     * @var non-empty-string
     */
    public string $id;

    /**
     * @var non-empty-lowercase-string
     */
    public string $mimeType;

    /**
     * @throws InvalidArgumentException when the file ID is empty
     * @throws InvalidArgumentException when the MIME type is empty
     */
    public function __construct(
        ?string $id,
        ?string $mimeType,
    ) {
        if ('' === $id = trim((string) $id)) {
            throw new InvalidArgumentException('The file ID cannot be empty.');
        }

        $this->id = $id;

        if ('' === $mimeType = trim((string) $mimeType)) {
            throw new InvalidArgumentException('The MIME type cannot be empty.');
        }

        $this->mimeType = strtolower($mimeType);
    }

    /**
     * @return non-empty-string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return non-empty-lowercase-string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }
}
