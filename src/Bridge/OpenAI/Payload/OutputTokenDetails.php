<?php

namespace OneToMany\AI\Bridge\OpenAI\Payload;

use OneToMany\AI\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class OutputTokenDetails
{
    /**
     * @param non-negative-int $reasoning_tokens
     */
    public function __construct(
        public int $reasoning_tokens = 0,
    ) {
    }
}
