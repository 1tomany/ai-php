<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses;

final readonly class Response
{
    /**
     * @param non-empty-string $id
     * @param positive-int $created_at
     * @param non-empty-string $status
     * @param list<ResponseOutputItem> $output
     */
    public function __construct(
        public string $id,
        public int $created_at,
        public string $status,
        public array $output = [],
        public ?ResponseError $error = null,
    ) {
    }

    /**
     * @return ?non-empty-string
     */
    public function getText(): ?string
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
    public function getError(): ?string
    {
        if ($this->error instanceof ResponseError) {
            return $this->error->message;
        }

        foreach ($this->output as $output) {
            $refusal = $output->refusal;

            if (null !== $refusal) {
                return $refusal;
            }
        }

        return null;
    }
}
