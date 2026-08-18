<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

use OneToMany\AI\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Attribute\SerializedName;

use function trim;

final readonly class File
{
    /**
     * @var non-empty-string
     */
    public string $id;

    /**
     * @throws UnexpectedValueException when the file ID is missing
     */
    public function __construct(
        string $id,
        public string $purpose,
        #[SerializedName('expires_at')]
        public ?int $expiresAt = null,
    ) {
        if ('' === $id = trim($id)) {
            throw new UnexpectedValueException('The OpenAI file response is missing its ID.');
        }

        $this->id = $id;
    }
}
