<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

use OneToMany\AI\Exception\InvalidArgumentException;

use function assert;
use function str_starts_with;

abstract readonly class FileContent extends Content implements \JsonSerializable
{
    /**
     * @see OneToMany\AI\Bridge\Gemini\Resource\Interaction\Content
     *
     * @param non-empty-lowercase-string $mime_type
     * @param ?non-empty-string $data
     * @param ?non-empty-string $uri
     *
     * @throws InvalidArgumentException when the data and URI are both empty
     */
    public function __construct(
        string $type,
        public string $mime_type,
        public ?string $data = null,
        public ?string $uri = null,
    ) {
        parent::__construct($type);

        if (null === $data && null === $uri) {
            throw new InvalidArgumentException('The data and URI cannot both be empty.');
        }

        assert(true === $this->isFile());
    }

    public static function create(
        string $mimeType,
        ?string $data = null,
        ?string $uri = null,
    ): self {
        if (str_starts_with($mimeType, 'audio/')) {
            return new AudioContent($mimeType, $data, $uri);
        }

        if (str_starts_with($mimeType, 'image/')) {
            return new ImageContent($mimeType, $data, $uri);
        }

        return new DocumentContent($mimeType, $data, $uri);
    }

    /**
     * @see \JsonSerializable
     *
     * @return array{
     *   type: 'audio'|'document'|'image',
     *   mime_type: non-empty-lowercase-string,
     *   data?: non-empty-string,
     *   uri?: non-empty-string,
     * }
     */
    #[\Override]
    public function jsonSerialize(): mixed
    {
        $json = [
            'type' => $this->type,
            'mime_type' => $this->mime_type,
        ];

        if (null !== $this->data) {
            $json['data'] = $this->data;
        }

        if (null !== $this->uri) {
            $json['uri'] = $this->uri;
        }

        return $json;
    }
}
