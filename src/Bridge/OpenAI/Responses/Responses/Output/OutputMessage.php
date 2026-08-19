<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses\Output;

use Symfony\Component\Serializer\Attribute\DiscriminatorMap;

#[DiscriminatorMap(
    typeProperty: 'type',
    mapping: [
        'refusal' => ResponseOutputRefusal::class,
        'output_text' => ResponseOutputText::class,
    ],
)]
abstract readonly class OutputMessage
{
}
