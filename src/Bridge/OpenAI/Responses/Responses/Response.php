<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses;

final class Response
{
    /**
     * @param non-empty-string $id
     * @param positive-int $created_at
     * @param non-empty-string $status
     * @param list<ResponseOutputItem> $output
     */
    public function __construct(
        public readonly string $id,
        public readonly int $created_at,
        public readonly string $status,
        public readonly array $output = [],
        public readonly ?ResponseError $error = null,
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

    /**
     * @var ?non-empty-string
     */
    public ?string $refusal {
        get => $this->compileRefusal();
    }

    /**
     * @return ?non-empty-string
     */
    private function compileText(): ?string
    {
        foreach ($this->output as $output) {
            $outputText = $output->text;

            if (null !== $outputText) {
                return $outputText;
            }
        }

        return null;
    }

    /**
     * @return ?non-empty-string
     */
    private function compileRefusal(): ?string
    {
        foreach ($this->output as $output) {
            $refusal = $output->refusal;

            if (null !== $refusal) {
                return $refusal;
            }
        }

        return null;
    }
}
