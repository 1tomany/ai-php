<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

use OneToMany\AI\Exception\InvalidArgumentException;

use function sprintf;

/**
 * @extends ResponseFormat<'text'>
 */
final readonly class TextResponseFormat extends ResponseFormat implements \JsonSerializable
{
    /**
     * @see OneToMany\AI\Bridge\Gemini\Resource\Interaction\ResponseFormat
     *
     * @param 'application/json'|'text/plain' $mime_type
     * @param ?array<string, mixed> $schema
     *
     * @throws InvalidArgumentException when the MIME type is "application/json" and a schema is not provided
     */
    public function __construct(
        public string $mime_type = 'application/json',
        public ?array $schema = null,
    ) {
        parent::__construct('text');

        if ($this->isMimeTypeApplicationJson() && null === $schema) {
            throw new InvalidArgumentException(sprintf('A schema is required when the MIME type is "%s".', $this->mime_type));
        }
    }

    /**
     * @phpstan-assert-if-true 'application/json' $this->mime_type
     */
    public function isMimeTypeApplicationJson(): bool
    {
        return 'application/json' === $this->mime_type;
    }

    /**
     * @see \JsonSerializable
     *
     * @return array{
     *   type: 'text',
     *   mime_type: 'application/json'|'text/plain',
     *   schema?: array<string, mixed>,
     * }
     */
    #[\Override]
    public function jsonSerialize(): mixed
    {
        $json = [
            'type' => $this->type,
            'mime_type' => $this->mime_type,
        ];

        if (null !== $this->schema) {
            $json['schema'] = $this->schema;
        }

        return $json;
    }
}
