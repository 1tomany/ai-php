<?php

namespace OneToMany\AI\Bridge\Gemini\Payload\Files;

final readonly class Upload
{
    public function __construct(
        public File $file,
    ) {
    }
}
