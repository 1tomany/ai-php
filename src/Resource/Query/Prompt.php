<?php

namespace OneToMany\AI\Resource\Query;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Resource\File\RemoteFile;

use function array_values;

final readonly class Prompt
{
    /**
     * @param non-empty-list<InputText|RemoteFile> $input
     */
    private function __construct(
        public array $input,
        public ?InputText $instructions = null,
        public ?JsonSchema $schema = null,
    ) {
    }

    /**
     * @throws InvalidArgumentException when no input is provided
     */
    public static function with(InputText|RemoteFile ...$inputs): self
    {
        if ([] === $inputs) {
            throw new InvalidArgumentException('At least one text or file input is required.');
        }

        return new self(array_values($inputs));
    }

    public function withInstructions(InputText $instructions): self
    {
        return new self($this->input, $instructions, $this->schema);
    }

    public function withSchema(JsonSchema $schema): self
    {
        return new self($this->input, $this->instructions, $schema);
    }
}
