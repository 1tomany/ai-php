<?php

namespace OneToMany\AI\Bridge\Gemini\Resource\Interaction;

/**
 * @template TType of 'audio'|'document'|'image'|'text'|'video'
 */
abstract readonly class Content
{
    /**
     * @param TType $type
     */
    public function __construct(
        public string $type,
    ) {
    }

    /**
     * @phpstan-assert-if-true 'audio'|'document'|'image' $this->type
     */
    public function isFile(): bool
    {
        return
            true === $this->isTypeAudio()
            || true === $this->isTypeDocument()
            || true === $this->isTypeImage()
        ;
    }

    /**
     * @phpstan-assert-if-true 'audio' $this->type
     */
    public function isTypeAudio(): bool
    {
        return 'audio' === $this->type;
    }

    /**
     * @phpstan-assert-if-true 'document' $this->type
     */
    public function isTypeDocument(): bool
    {
        return 'document' === $this->type;
    }

    /**
     * @phpstan-assert-if-true 'image' $this->type
     */
    public function isTypeImage(): bool
    {
        return 'image' === $this->type;
    }

    /**
     * @phpstan-assert-if-true 'text' $this->type
     */
    public function isTypeText(): bool
    {
        return 'text' === $this->type;
    }
}
