<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

use OneToMany\AI\Exception\InvalidArgumentException;

use function assert;

abstract readonly class FileContent extends Content implements \JsonSerializable
{
    /**
     * @see OneToMany\AI\Bridge\Gemini\Resource\Interaction\Content
     *
     * @param ?non-empty-string $data
     * @param ?non-empty-string $uri
     *
     * @throws InvalidArgumentException when the data and URI are both empty
     */
    public function __construct(
        string $type,
        public ?string $data,
        public ?string $uri,
    ) {
        parent::__construct($type);

        if (null === $data && null === $uri) {
            throw new InvalidArgumentException('The data and URI cannot both be empty.');
        }

        assert(true === $this->isFile());
    }

    /**
     * @see \JsonSerializable
     *
     * @return array{
     *   type: 'audio'|'document'|'image',
     *   data?: non-empty-string,
     *   uri?: non-empty-string,
     * }
     */
    #[\Override]
    public function jsonSerialize(): mixed
    {
        $json = ['type' => $this->type];

        if (null !== $this->data) {
            $json['data'] = $this->data;
        }

        if (null !== $this->uri) {
            $json['uri'] = $this->uri;
        }

        return $json;
    }
}
