<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses\Enum;

enum ResponseStatus: string
{
    case Completed = 'completed';
    case Failed = 'failed';
    case InProgress = 'in_progress';
    case Cancelled = 'cancelled';
    case Queued = 'queued';
    case Incomplete = 'incomplete';
}
