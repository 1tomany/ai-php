<?php

namespace OneToMany\AI\Inference;

use OneToMany\AI\Exception\InvalidArgumentException;

use function file_get_contents;
use function is_array;
use function is_string;
use function json_decode;
use function sprintf;
use function trim;

use const JSON_THROW_ON_ERROR;

final readonly class JsonSchema
{
    /**
     * @var non-empty-string
     */
    public string $name;

    /**
     * @param array<string, mixed> $schema
     */
    public function __construct(
        public array $schema,
        ?string $name = null,
        public bool $strict = true,
    ) {
        $name ??= is_string($schema['title'] ?? null) ? $schema['title'] : null;

        if ('' === $name = trim((string) $name)) {
            throw new InvalidArgumentException('A JSON schema requires a name or non-empty "title" property.');
        }

        $this->name = $name;
    }

    public static function fromFile(string $path, ?string $name = null, bool $strict = true): self
    {
        if (false === $contents = @file_get_contents($path)) {
            throw new InvalidArgumentException(sprintf('Reading the JSON schema "%s" failed.', $path));
        }

        try {
            $schema = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidArgumentException(sprintf('Decoding the JSON schema "%s" failed.', $path), previous: $e);
        }

        if (!is_array($schema)) {
            throw new InvalidArgumentException(sprintf('The JSON schema "%s" must contain an object.', $path));
        }

        $object = [];

        foreach ($schema as $key => $value) {
            if (!is_string($key)) {
                throw new InvalidArgumentException(sprintf('The JSON schema "%s" must contain an object.', $path));
            }

            $object[$key] = $value;
        }

        return new self($object, $name, $strict);
    }
}
