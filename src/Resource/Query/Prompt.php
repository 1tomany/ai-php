<?php

namespace OneToMany\AI\Resource\Query;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Resource\File\RemoteFile;

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

    /**
     * @throws InvalidArgumentException when no input is provided
     */
    public static function with(string|RemoteFile ...$inputs): self
    {
        $prompts = [];

        foreach ($inputs as $input) {
            if (is_string($input)) {
                $input = trim($input);

                if ('' !== $input) {
                    $prompts[] = $input;
                }
            } else {
                $prompts[] = $input;
            }
        }

        if ([] === $prompts) {
            throw new InvalidArgumentException('At least one text or file input is required.');
        }

        return new self($prompts);
    }

    /**
     * @throws InvalidArgumentException when the instructions are empty
     */
    public function withInstructions(string $instructions): self
    {
        if ('' === $instructions = trim($instructions)) {
            throw new InvalidArgumentException('The instructions cannot be empty.');
        }

        return new self($this->input, $instructions, $this->schema);
    }

    public function withSchema(JsonSchema $schema): self
    {
        return new self($this->input, $this->instructions, $schema);
    }
}
