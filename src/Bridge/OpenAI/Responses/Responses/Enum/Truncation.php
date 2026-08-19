<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses\Enum;

enum Truncation: string
{
    case Auto = 'auto';
    case Disabled = 'disabled';
}
