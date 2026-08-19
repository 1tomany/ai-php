<?php

namespace OneToMany\AI\Bridge\Gemini\Responses\Interactions;

use function trim;

final class Interaction
{
    /**
     * @param non-empty-string $id
     * @param 'in_progress'|'requires_action'|'completed'|'failed'|'cancelled'|'incomplete'|'budget_exceeded'|'queued' $status
     * @param list<Step> $steps
     * @param list<Error> $errors
     */
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly array $steps = [],
        public readonly array $errors = [],
    ) {
    }

    public bool $completed {
        get => 'completed' === $this->status;
    }

    /**
     * @var ?non-empty-string
     */
    public ?string $text {
        get => $this->compileText();
    }

    public ?Error $error {
        get => $this->findError();
    }

    /**
     * @return ?non-empty-string
     */
    private function compileText(): ?string
    {
        $outputText = null;

        foreach ($this->steps as $step) {
            if (null !== $step->text) {
                $outputText .= "{$step->text}\n";
            }
        }

        $outputText = trim((string) $outputText);

        return '' !== $outputText ? $outputText : null;
    }

    private function findError(): ?Error
    {
        foreach ($this->errors as $error) {
            if (null !== $error->message && '' !== trim($error->message)) {
                return $error;
            }
        }

        return null;
    }
}
