<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses\Enum;

enum ResponseOutputMessageStatus: string
{
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Incomplete = 'incomplete';
}
