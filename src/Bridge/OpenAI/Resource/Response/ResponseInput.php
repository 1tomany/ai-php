<?php

namespace OneToMany\AI\Bridge\OpenAI\Resource\Response;

/**
 * @template TType of 'input_file'|'input_image'|'input_text'
 */
abstract readonly class ResponseInput implements \JsonSerializable
{
    /**
     * @param TType $type
     */
    public function __construct(
        public string $type,
    ) {
    }
}
