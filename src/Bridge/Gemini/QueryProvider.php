<?php

namespace OneToMany\AI\Bridge\Gemini;

use OneToMany\AI\Bridge\Common\Trait\QueryTrait;
use OneToMany\AI\Bridge\Gemini\Response\Interaction\Interaction as ResponsePayload;
use OneToMany\AI\Contract\Bridge\QueryProviderInterface;
use OneToMany\AI\Resource\Query\Query;
use OneToMany\AI\Resource\Query\Response;

final readonly class QueryProvider extends AbstractProvider implements QueryProviderInterface
{
    use QueryTrait;

    /**
     * @see OneToMany\AI\Contract\Bridge\QueryProviderInterface
     */
    #[\Override]
    public function run(Query $query): Response
    {
        $url = $this->url($this->apiVersion, 'interactions');

        try {
            $response = $this->transport->postRequest($url, [
                'headers' => [
                    'x-goog-api-key' => $this->apiKey,
                ],
                'json' => $query->request,
            ]);

            $record = $this->transport->decode($response, ResponsePayload::class);
        } finally {
            unset($query);
        }

        return new Response(static::getVendor(), $record->id, $record->completed, $record->text, null, $record->error?->message);
    }
}
