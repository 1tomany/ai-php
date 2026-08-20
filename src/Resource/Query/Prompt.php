<?php

namespace OneToMany\AI\Resource\Query;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Resource\File\RemoteFile;

use function is_string;

final class Prompt
{
    /**
     * @var list<InputText|RemoteFile>
     */
    private array $input = [];

    private ?InputText $instructions = null;

    private ?JsonSchema $schema = null;

    public function __construct()
    {
    }

    /**
     * @throws InvalidArgumentException when no input is provided
     */
    public static function with(string|InputText|RemoteFile ...$inputs): static
    {
        if ([] === $inputs) {
            throw new InvalidArgumentException('At least one text or file input is required.');
        }

        $prompt = new static();

        foreach ($inputs as $input) {
            if (true === is_string($input)) {
                $input = new InputText($input);
            }

            $prompt = $prompt->addInput($input);
        }

        return $prompt;
    }

    public function addInputText(string|InputText $text): static
    {
        return $this->addInput(is_string($text) ? new InputText($text) : $text);
    }

    public function addRemoteFile(RemoteFile $file): static
    {
        return $this->addInput($file);
    }

    public function withInstructions(string|InputText $instructions): static
    {
        if (!$instructions instanceof InputText) {
            $instructions = new InputText($instructions);
        }

        $prompt = clone $this;
        $prompt->instructions = $instructions;

        return $prompt;
    }

    /**
     * @param array<string, mixed> $schema
     */
    public function withJsonSchema(
        ?string $name,
        array $schema,
        bool $strict = true,
    ): self {
        return $this->withSchema(new JsonSchema($name, $strict, schema: $schema));
    }

    public function withJsonSchemaFile(string $path, ?string $name = null): static
    {
        return $this->withSchema(JsonSchema::fromFile($name, $path));
    }

    /**
     * @return list<InputText|RemoteFile>
     */
    public function input(): array
    {
        return $this->input;
    }

    public function instructions(): ?InputText
    {
        return $this->instructions;
    }

    public function schema(): ?JsonSchema
    {
        return $this->schema;
    }

    public function isEmpty(): bool
    {
        return [] === $this->input;
    }

    private function addInput(InputText|RemoteFile $input): static
    {
        $prompt = clone $this;
        $prompt->input[] = $input;

        return $prompt;
    }

    private function withSchema(JsonSchema $schema): static
    {
        $prompt = clone $this;
        $prompt->schema = $schema;

        return $prompt;
    }
}
