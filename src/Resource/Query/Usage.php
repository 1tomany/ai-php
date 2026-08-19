<?php

namespace OneToMany\AI\Resource\Query;

final readonly class Usage
{
    /**
     * @param non-negative-int $inputTokens
     * @param non-negative-int $cachedInputTokens
     * @param non-negative-int $reasoningTokens
     * @param non-negative-int $outputTokens
     * @param non-negative-int $totalTokens
     */
    public function __construct(
        public int $inputTokens = 0,
        public int $cachedInputTokens = 0,
        public int $reasoningTokens = 0,
        public int $outputTokens = 0,
        public int $totalTokens = 0,
    ) {
    }
}
