<?php

namespace OneToMany\AI\Bridge\Gemini\Payload;

final readonly class FileResponse
{
    public function __construct(
        public File $file,
    ) {
    }
}
