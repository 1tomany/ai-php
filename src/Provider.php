<?php

namespace OneToMany\AI;

enum Provider: string
{
    case Gemini = 'gemini';
    case OpenAI = 'openai';

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return non-empty-lowercase-string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
