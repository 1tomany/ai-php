<?php

namespace OneToMany\AI\Bridge\Gemini\Payload\Files;

use OneToMany\AI\Bridge\Gemini\Payload\Files\File;

final readonly class Upload
{
    public function __construct(
        public File $file,
    ) {
    }
}
