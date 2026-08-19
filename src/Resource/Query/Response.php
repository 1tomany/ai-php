<?php

namespace OneToMany\AI\Resource\Query;

use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Provider;

use function is_array;
use function json_decode;

use const JSON_THROW_ON_ERROR;

final readonly class Response
{
    /**
     * @param non-empty-string $id
     * @param ?non-empty-string $text
     * @param ?non-empty-string $refusal
     * @param ?non-empty-string $error
     */
    public function __construct(
        public Provider $provider,
        public string $id,
        public bool $completed,
        public ?string $text = null,
        public ?string $refusal = null,
        public ?string $error = null,
        public Usage $usage = new Usage(),
    ) {
    }

    /**
     * @return ?array<array-key, mixed>
     *
     * @throws RuntimeException when decoding the response as JSON fails
     * @throws RuntimeException when the decoded response is not an object or array
     */
    public function toArray(): ?array
    {
        if (!$this->text) {
            return null;
        }

        try {
            $array = json_decode($this->text, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new RuntimeException('Decoding the model output as JSON failed.', previous: $e);
        }

        if (!is_array($array)) {
            throw new RuntimeException('The model output did not contain a JSON object or array.');
        }

        return $array;
    }
}
