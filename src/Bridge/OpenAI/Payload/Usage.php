<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

use OneToMany\AI\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Attribute\SerializedName;

use function sprintf;

final readonly class Usage
{
    /** @var non-negative-int */
    #[SerializedName('input_tokens')]
    public int $inputTokens;

    /** @var non-negative-int */
    #[SerializedName('output_tokens')]
    public int $outputTokens;

    /** @var non-negative-int */
    #[SerializedName('total_tokens')]
    public int $totalTokens;

    public function __construct(
        int $inputTokens = 0,
        int $outputTokens = 0,
        int $totalTokens = 0,
        #[SerializedName('input_tokens_details')]
        public ?InputTokenDetails $inputTokenDetails = null,
        #[SerializedName('output_tokens_details')]
        public ?OutputTokenDetails $outputTokenDetails = null,
    ) {
        $this->inputTokens = $this->tokens($inputTokens, 'input_tokens');
        $this->outputTokens = $this->tokens($outputTokens, 'output_tokens');
        $this->totalTokens = $this->tokens($totalTokens, 'total_tokens');
    }

    /**
     * @return non-negative-int
     */
    private function tokens(int $tokens, string $field): int
    {
        if ($tokens < 0) {
            throw new UnexpectedValueException(sprintf('The OpenAI response contains invalid "%s" usage.', $field));
        }

        return $tokens;
    }
}
