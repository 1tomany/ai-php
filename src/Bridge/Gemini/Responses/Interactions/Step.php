<?php

namespace OneToMany\AI\Bridge\Gemini\Responses\Interactions;

use function trim;

final class Step
{
    /**
     * @param non-empty-string $type
     * @param list<Content> $content
     */
    public function __construct(
        public readonly string $type,
        public readonly array $content = [],
    ) {
    }

    /**
     * @var ?non-empty-string
     */
    public ?string $text {
        get => $this->compileText();
    }

    /**
     * @phpstan-assert-if-true 'model_output' $this->type
     */
    public function isTypeModelOutput(): bool
    {
        return 'model_output' === $this->type;
    }

    /**
     * @return ?non-empty-string
     */
    private function compileText(): ?string
    {
        $text = null;

        if ($this->isTypeModelOutput()) {
            foreach ($this->content as $content) {
                if ($content->isTypeText()) {
                    $text .= $content->text;
                }
            }

            $text = trim((string) $text);
        }

        return '' !== $text ? $text : null;
    }
}
