<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

final readonly class DocumentContent extends FileContent
{
    /**
     * @see OneToMany\AI\Bridge\Gemini\Resource\Interaction\Content
     */
    public function __construct(
        string $mime_type,

        ?string $data = null,
        ?string $uri = null,
    ) {
        parent::__construct('document', $mime_type, $data, $uri);
    }
}
