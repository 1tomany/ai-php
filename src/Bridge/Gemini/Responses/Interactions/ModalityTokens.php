<?php

namespace OneToMany\AI\Bridge\Gemini\Responses\Interactions;

use function strtolower;

final readonly class ModalityTokens
{
    /**
     * @var non-empty-lowercase-string
     */
    public string $modality;

    /**
     * @param non-empty-string $modality
     * @param non-negative-int $tokens
     */
    public function __construct(
        string $modality,
        public int $tokens = 0,
    ) {
        $this->modality = strtolower($modality);
    }
}
