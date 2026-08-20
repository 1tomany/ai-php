<?php

namespace OneToMany\AI\Bridge\OpenAI\Resource\Response;

use function str_starts_with;

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

    /**
     * @param non-empty-lowercase-string $mimeType
     * @param non-empty-string $file_id
     *
     * @return ResponseInputFile|ResponseInputImage
     */
    public static function asFile(
        string $mimeType,
        string $file_id,
    ): self {
        if (str_starts_with($mimeType, 'image')) {
            return new ResponseInputImage($file_id);
        }

        return new ResponseInputFile($file_id);
    }
}
