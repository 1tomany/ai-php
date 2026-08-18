<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

use OneToMany\AI\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class OutputTokenDetails
{
    /**
     * @var non-negative-int
     */
    #[SerializedName('reasoning_tokens')]
    public int $reasoningTokens;

    public function __construct(
        int $reasoningTokens = 0,
    ) {
        if ($reasoningTokens < 0) {
            throw new UnexpectedValueException('The OpenAI response contains invalid "reasoning_tokens" usage.');
        }

        $this->reasoningTokens = $reasoningTokens;
    }
}
