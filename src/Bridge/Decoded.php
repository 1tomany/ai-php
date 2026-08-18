<?php

namespace OneToMany\AI\Bridge;

/**
 * @template T of object
 */
final readonly class Decoded
{
    /**
     * @param T $payload
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public object $payload,
        public array $raw,
    ) {
    }
}
