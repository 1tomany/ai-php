<?php

namespace OneToMany\AI\Resource\Query;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Resource\File\RemoteFile;

use function is_string;
use function trim;

final class Prompt
{
    /**
     * @var list<InputText|RemoteFile>
     */
    private array $input = [];

    private ?InputText $instructions = null;

    private ?JsonSchema $schema = null;

    public function __construct(string|InputText|null $input = null)
    {
        if (is_string($input)) {
            $input = trim($input);
        }

        if (null !== $input && '' !== $input) {
            $this->addInputText(text: $input);
        }
    }

    /**
     * @throws InvalidArgumentException when no input is provided
     */
    public static function with(InputText|RemoteFile ...$inputs): self
    {
        if ([] === $inputs) {
            throw new InvalidArgumentException('At least one text or file input is required.');
        }

        $prompt = new self();

        foreach ($inputs as $input) {
            $prompt = $prompt->addInput($input);
        }

        return $prompt;
    }

    public function addInputText(string|InputText $text): self
    {
        return $this->addInput(is_string($text) ? new InputText($text) : $text);
    }

    public function addRemoteFile(RemoteFile $file): self
    {
        return $this->addInput($file);
    }

    public function withInstructions(string|InputText $instructions): self
    {
        $prompt = clone $this;
        $prompt->instructions = is_string($instructions) ? new InputText($instructions) : $instructions;

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
        return $this->withSchema(new JsonSchema($name, strict: $strict, schema: $schema));
    }

    public function withJsonSchemaFile(string $path, ?string $name = null): self
    {
        return $this->withSchema(JsonSchema::fromFile($name, $path));
    }

    public function withSchema(JsonSchema $schema): self
    {
        $prompt = clone $this;
        $prompt->schema = $schema;

        return $prompt;
    }

    public function isEmpty(): bool
    {
        return [] === $this->input;
    }

    /**
     * @return list<InputText|RemoteFile>
     */
    public function getInput(): array
    {
        return $this->input;
    }

    public function getInstructions(): ?InputText
    {
        return $this->instructions;
    }

    public function getSchema(): ?JsonSchema
    {
        return $this->schema;
    }

    private function addInput(InputText|RemoteFile $input): self
    {
        $prompt = clone $this;
        $prompt->input[] = $input;

        return $prompt;
    }
}
