<?php

namespace OneToMany\AI\Inference;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\File\RemoteFile;

use function array_values;
use function trim;

final readonly class Prompt
{
    /**
     * @param non-empty-list<string|RemoteFile> $input
     */
    private function __construct(
        public array $input,
        public ?string $instructions = null,
        public ?JsonSchema $schema = null,
    ) {
    }

    public static function with(string|RemoteFile ...$input): self
    {
        if ([] === $input) {
            throw new InvalidArgumentException('A prompt requires at least one input.');
        }

        foreach ($input as $part) {
            if (is_string($part) && '' === trim($part)) {
                throw new InvalidArgumentException('Prompt text cannot be empty.');
            }
        }

        return new self(array_values($input));
    }

    public function withInstructions(string $instructions): self
    {
        if ('' === $instructions = trim($instructions)) {
            throw new InvalidArgumentException('Prompt instructions cannot be empty.');
        }

        return new self($this->input, $instructions, $this->schema);
    }

    public function withSchema(JsonSchema $schema): self
    {
        return new self($this->input, $this->instructions, $schema);
    }
}
