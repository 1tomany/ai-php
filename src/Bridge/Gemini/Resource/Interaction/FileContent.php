<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

use function str_starts_with;
use function trim;

/**
 * @template TType of 'audio'|'document'|'image'
 *
 * @extends Content<TType>
 */
abstract readonly class FileContent extends Content implements \JsonSerializable
{
    /**
     * @var non-empty-string
     */
    public string $uri;

    /**
     * @see OneToMany\AI\Bridge\Gemini\Resource\Interaction\Content
     *
     * @param TType $type
     * @param non-empty-lowercase-string $mimeType
     *
     * @throws \InvalidArgumentException when the URI is empty
     */
    public function __construct(
        string $type,
        ?string $uri,
        public string $mimeType,
    ) {
        parent::__construct($type);

        if ('' === trim((string) $uri)) {
            throw new \InvalidArgumentException('The URI cannot be empty.');
        }

        $this->uri = $uri;
    }

    /**
     * @param ?non-empty-string $uri
     * @param non-empty-lowercase-string $mimeType
     *
     * @return AudioContent|DocumentContent|ImageContent
     */
    public static function create(
        ?string $uri,
        string $mimeType,
    ): self {
        if (str_starts_with($mimeType, 'audio')) {
            return new AudioContent($uri, $mimeType);
        }

        if (str_starts_with($mimeType, 'image')) {
            return new ImageContent($uri, $mimeType);
        }

        return new DocumentContent($uri, $mimeType);
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
