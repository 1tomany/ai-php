<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

use OneToMany\AI\Exception\InvalidArgumentException;

use function trim;

final readonly class File
{
    /**
     * @var non-empty-string
     */
    public string $id;

    /**
     * @throws InvalidArgumentException when the file ID is empty
     */
    public function __construct(
        string $id,
        public string $purpose,
        public ?int $expires_at = null,
    ) {
        if ('' === $id = trim($id)) {
            throw new InvalidArgumentException('The file ID cannot be empty.');
        }

        $this->id = $id;
    }
}
