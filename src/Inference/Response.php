<?php

namespace OneToMany\AI\Inference;

use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Provider;

use function is_array;
use function json_decode;

use const JSON_THROW_ON_ERROR;

final readonly class Response
{
    /**
     * @param non-empty-string $id
     * @param non-empty-string $status
     * @param ?non-empty-string $text
     * @param ?non-empty-string $refusal
     * @param ?non-empty-string $error
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public Provider $provider,
        public string $id,
        public string $status,
        public ?string $text,
        public ?string $refusal,
        public ?string $error,
        public Usage $usage,
        public array $raw,
    ) {
    }

    public function completed(): bool
    {
        return 'completed' === $this->status;
    }

    /**
     * @return non-empty-string
     */
    public function text(): string
    {
        if (!empty($this->text)) {
            return $this->text;
        }

        throw new RuntimeException($this->error ?? $this->refusal ?? 'The model did not return text.');
    }

    /**
     * @return array<array-key, mixed>
     */
    public function json(): array
    {
        try {
            $decoded = json_decode($this->text(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new RuntimeException('Decoding the model response as JSON failed.', previous: $e);
        }

        if (!is_array($decoded)) {
            throw new RuntimeException('The model response did not contain a JSON object or array.');
        }

        return $decoded;
    }
}
