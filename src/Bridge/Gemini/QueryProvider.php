<?php

namespace OneToMany\AI\Bridge\Gemini;

use OneToMany\AI\Bridge\Gemini\Normalizer\QueryRequestNormalizer;
use OneToMany\AI\Bridge\Gemini\Responses\Interactions\Interaction as ResponsePayload;
use OneToMany\AI\Bridge\QueryCompilerTrait;
use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Bridge\QueryProviderInterface;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\Query\Query;
use OneToMany\AI\Resource\Query\Response;

final readonly class QueryProvider implements QueryProviderInterface
{
    use QueryCompilerTrait;

    public function __construct(
        private Transport $transport,
        private QueryRequestNormalizer $normalizer,
        private string $apiVersion = 'v1beta',
    ) {
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\QueryProviderInterface
     */
    #[\Override]
    public function provider(): Provider
    {
        return Provider::Gemini;
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\QueryProviderInterface
     */
    #[\Override]
    public function run(Query $query): Response
    {
        $url = $this->transport->url($this->apiVersion, 'interactions');

        try {
            $response = $this->transport->postRequest($url, [
                'json' => $query->request,
            ]);

            $record = $this->transport->decode($response, ResponsePayload::class);
        } finally {
            unset($query);
        }

        return new Response($this->provider(), $record->id, $record->completed, $record->text, null, $record->error?->message);
    }
}
