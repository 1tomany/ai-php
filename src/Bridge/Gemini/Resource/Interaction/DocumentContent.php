<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

/**
 * @extends FileContent<'document'>
 */
final readonly class DocumentContent extends FileContent
{
    /**
     * @see OneToMany\AI\Bridge\Gemini\Resource\Interaction\FileContent
     */
    public function __construct(
        string $mime_type,
        ?string $uri = null,
        ?string $data = null,
    ) {
        parent::__construct('document', $mime_type, $uri, $data);
    }
}
