<?php

namespace OneToMany\AI\Bridge\OpenAI\Responses\Responses;

use Symfony\Component\Serializer\Attribute\DiscriminatorMap;

#[DiscriminatorMap(
    typeProperty: 'type',
    mapping: [
        'reasoning' => Reasoning::class,
        'message' => ResponseOutputMessage::class,
    ],
)]
abstract readonly class ResponseOutputItem
{
}
