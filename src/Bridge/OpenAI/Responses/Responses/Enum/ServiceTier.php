<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses\Enum;

enum ServiceTier: string
{
    case Auto = 'auto';
    case Default = 'default';
    case Flex = 'flex';
    case Scale = 'scale';
    case Priority = 'priority';
    case Fast = 'fast';
    case Ultrafast = 'ultrafast';
}
