<?php

namespace OneToMany\AI\Resource\Query;

use OneToMany\AI\Exception\InvalidArgumentException;

use function is_string;

final class Prompt
{
    /**
     * @var list<InputFile|InputText>
     */
    private array $inputs = [];
    private ?InputText $instructions = null;
    private ?JsonSchema $schema = null;

    public function __construct()
    {
    }

    /**
     * @throws InvalidArgumentException when no input is provided
     */
    public static function with(string|InputFile|InputText ...$inputs): static
    {
        if ([] === $inputs) {
            throw new InvalidArgumentException('At least one file or text input is required.');
        }

        $prompt = new static();

        foreach ($inputs as $input) {
            $prompt = $prompt->addInput($input);
        }

        return $prompt;
    }

    public function addInputText(string|InputText $text): static
    {
        return $this->addInput($text);
    }

    public function addInputFile(InputFile $file): static
    {
        return $this->addInput($file);
    }

    public function withInstructions(string|InputText $text): static
    {
        if (!$text instanceof InputText) {
            $text = new InputText($text);
        }

        $prompt = clone $this;
        $prompt->instructions = $text;

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
        return $this->withSchema(new JsonSchema($name, $schema, $strict));
    }

    public function withJsonSchemaFile(string $file, ?string $name = null): static
    {
        return $this->withSchema(JsonSchema::fromFile($file, $name));
    }

    /**
     * @return list<InputText|InputFile>
     */
    public function getInputs(): array
    {
        return $this->inputs;
    }

    public function getInstructions(): ?InputText
    {
        return $this->instructions;
    }

    public function getSchema(): ?JsonSchema
    {
        return $this->schema;
    }

    public function isEmpty(): bool
    {
        return [] === $this->inputs;
    }

    private function addInput(string|InputFile|InputText $input): static
    {
        $prompt = clone $this;
        $prompt->inputs[] = is_string($input) ? new InputText($input) : $input;

        return $prompt;
    }

    private function withSchema(JsonSchema $schema): static
    {
        $prompt = clone $this;
        $prompt->schema = $schema;

        return $prompt;
    }
}
