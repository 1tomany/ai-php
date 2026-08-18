<?php

namespace OneToMany\AI\Bridge\Gemini\Payload;

use OneToMany\AI\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Attribute\SerializedName;

use function sprintf;

final readonly class Usage
{
    /** @var non-negative-int */
    #[SerializedName('total_input_tokens')]
    public int $inputTokens;

    /** @var non-negative-int */
    #[SerializedName('total_output_tokens')]
    public int $outputTokens;

    /** @var non-negative-int */
    #[SerializedName('total_cached_tokens')]
    public int $cachedTokens;

    /** @var non-negative-int */
    #[SerializedName('total_thought_tokens')]
    public int $thoughtTokens;

    /** @var non-negative-int */
    #[SerializedName('total_tokens')]
    public int $totalTokens;

    public function __construct(
        int $inputTokens = 0,
        int $outputTokens = 0,
        int $cachedTokens = 0,
        int $thoughtTokens = 0,
        int $totalTokens = 0,
    ) {
        $this->inputTokens = $this->tokens($inputTokens, 'total_input_tokens');
        $this->outputTokens = $this->tokens($outputTokens, 'total_output_tokens');
        $this->cachedTokens = $this->tokens($cachedTokens, 'total_cached_tokens');
        $this->thoughtTokens = $this->tokens($thoughtTokens, 'total_thought_tokens');
        $this->totalTokens = $this->tokens($totalTokens, 'total_tokens');
    }

    /**
     * @return non-negative-int
     */
    private function tokens(int $tokens, string $field): int
    {
        if ($tokens < 0) {
            throw new UnexpectedValueException(sprintf('The Gemini response contains invalid "%s" usage.', $field));
        }

        return $tokens;
    }
}
