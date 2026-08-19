<?php

namespace OneToMany\AI\Bridge\Gemini\Payload;

final readonly class Upload
{
    public function __construct(
        public File $file,
    ) {
    }
}
