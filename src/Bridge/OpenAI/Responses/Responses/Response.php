<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses;

final readonly class Response
{
    /**
     * @param non-empty-string $id
     * @param positive-int $created_at
     * @param list<ResponseOutputItem> $output
     */
    public function __construct(
        public string $id,
        public int $created_at,
        public ?ResponseError $error,
        public array $output,
        public ?string $status,
    ) {
    }

    /**
     * @return ?non-empty-string
     */
    public function getText(): ?string
    {
        // foreach ($this->output as $output) {
        //     if ($output instanceof ResponseOutputMessage) {
        //         if (null !== $output->getText()) {
        //             return $output->getText();
        //         }
        //     }
        // }

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

        // foreach ($this->output as $output) {
        //     if ($output instanceof ResponseOutputMessage) {
        //         if (null !== $output->getRefusal()) {
        //             return $output->getRefusal();
        //         }
        //     }
        // }

        return null;
    }
}
