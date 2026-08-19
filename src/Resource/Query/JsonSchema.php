<?php

namespace OneToMany\AI\Resource\Query;

use OneToMany\AI\Exception\InvalidArgumentException;

use function array_keys;
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
     * @param non-empty-string $mediaType
     * @param array<string, mixed> $schema
     *
     * @throws InvalidArgumentException when the schema has no name or "title" property
     */
    public function __construct(
        ?string $name,
        public bool $strict = true,
        public string $mediaType = 'application/json',
        public array $schema = [],
    ) {
        if (null !== $name) {
            $name = trim($name);
        }

        if (null === $name || '' === $name) {
            $schemaTitle = $schema['title'];

            if (is_string($schemaTitle)) {
                $name = trim($schemaTitle);
            }
        }

        if (null === $name || '' === $name) {
            throw new InvalidArgumentException('A JSON schema requires a name or non-empty "title" property.');
        }

        $this->name = $name;
    }

    /**
     * @throws InvalidArgumentException when reading the schema file fails
     * @throws InvalidArgumentException when decoding the schema fails
     * @throws InvalidArgumentException when the schema does not contain an object
     * @throws InvalidArgumentException when the schema has no name
     */
    public static function fromFile(?string $name, string $path): self
    {
        if (false === $contents = @file_get_contents($path)) {
            throw new InvalidArgumentException(sprintf('Reading the JSON schema file "%s" failed.', $path));
        }

        try {
            $schema = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidArgumentException(sprintf('Decoding the JSON schema file "%s" failed.', $path), previous: $e);
        }

        $isObject = true;

        if (is_array($schema)) {
            $keys = array_keys($schema);

            foreach ($keys as $key) {
                if (!is_string($key)) {
                    $isObject = false;
                }

                if (!$isObject) {
                    break;
                }
            }
        } else {
            $isObject = false;
        }

        if (false === $isObject) {
            throw new InvalidArgumentException(sprintf('The JSON schema file "%s" must contain an object.', $path));
        }

        return new self($name, schema: $schema); // @phpstan-ignore argument.type
    }
}
