<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

use OneToMany\AI\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class InputTokenDetails
{
    /**
     * @var non-negative-int
     */
    #[SerializedName('cached_tokens')]
    public int $cachedTokens;

    public function __construct(
        int $cachedTokens = 0,
    ) {
        if ($cachedTokens < 0) {
            throw new UnexpectedValueException('The OpenAI response contains invalid "cached_tokens" usage.');
        }

        $this->cachedTokens = $cachedTokens;
    }
}
