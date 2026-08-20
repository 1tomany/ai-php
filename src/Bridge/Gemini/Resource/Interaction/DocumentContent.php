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
        string $mimeType,
        ?string $uri,
    ) {
        parent::__construct('document', $mimeType, $uri);
    }
}
