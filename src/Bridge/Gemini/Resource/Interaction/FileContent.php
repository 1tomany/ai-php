<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

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
     * @param non-empty-lowercase-string $mimeType
     * @param ?non-empty-string $uri
     */
    public function __construct(
        string $type,
        public string $mimeType,
        public ?string $uri = null,
    ) {
        parent::__construct($type);
    }

    /**
     * @param non-empty-lowercase-string $mimeType
     * @param ?non-empty-string $uri
     *
     * @return AudioContent|DocumentContent|ImageContent
     */
    public static function create(
        string $mimeType,
        ?string $uri,
    ): self {
        if (str_starts_with($mimeType, 'audio')) {
            return new AudioContent($mimeType, $uri);
        }

        if (str_starts_with($mimeType, 'image')) {
            return new ImageContent($mimeType, $uri);
        }

        return new DocumentContent($mimeType, $uri);
    }

    /**
     * @see \JsonSerializable
     *
     * @return array{
     *   type: 'audio'|'document'|'image',
     *   uri: ?non-empty-string,
     *   mime_type: non-empty-lowercase-string,
     * }
     */
    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'uri' => $this->uri,
            'mime_type' => $this->mimeType,
        ];
    }
}
