<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

use OneToMany\AI\Exception\InvalidArgumentException;

use function str_starts_with;

/**
 * @template TType of 'audio'|'document'|'image'
 *
 * @extends Content<TType>
 */
abstract readonly class FileContent extends Content implements \JsonSerializable
{
    /**
     * @see OneToMany\AI\Bridge\Gemini\Resource\Interaction\Content
     *
     * @param TType $type
     * @param non-empty-lowercase-string $mime_type
     * @param ?non-empty-string $uri
     * @param ?non-empty-string $data
     *
     * @throws InvalidArgumentException when the data and URI are both empty
     */
    public function __construct(
        string $type,
        public string $mime_type,
        public ?string $uri = null,
        public ?string $data = null,
    ) {
        parent::__construct($type);

        if (null === $data && null === $uri) {
            throw new InvalidArgumentException('The data and URI cannot both be empty.');
        }
    }

    /**
     * @param non-empty-lowercase-string $mimeType
     * @param ?non-empty-string $uri
     * @param ?non-empty-string $data
     *
     * @return AudioContent|DocumentContent|ImageContent
     */
    public static function create(
        string $mimeType,
        ?string $uri = null,
        ?string $data = null,
    ): self {
        if (str_starts_with($mimeType, 'audio')) {
            return new AudioContent($mimeType, $uri, $data);
        }

        if (str_starts_with($mimeType, 'image')) {
            return new ImageContent($mimeType, $uri, $data);
        }

        return new DocumentContent($mimeType, $uri, $data);
    }

    /**
     * @see \JsonSerializable
     *
     * @return array{
     *   type: 'audio'|'document'|'image',
     *   uri: ?non-empty-string,
     *   data: ?non-empty-string,
     *   mime_type: non-empty-lowercase-string,
     * }
     */
    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'uri' => $this->uri,
            'data' => $this->data,
            'mime_type' => $this->mime_type,
        ];
    }
}
