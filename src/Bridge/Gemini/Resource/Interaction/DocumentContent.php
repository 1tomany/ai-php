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
        ?string $uri,
        string $mimeType,
    ) {
        parent::__construct('document', $uri, $mimeType);
    }
}
