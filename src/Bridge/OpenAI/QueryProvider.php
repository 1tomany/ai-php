<?php

namespace OneToMany\AI\Bridge\OpenAI;

use OneToMany\AI\Bridge\OpenAI\Normalizer\QueryRequestNormalizer;
use OneToMany\AI\Bridge\OpenAI\Responses\Responses\Response as ResponsePayload;
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
        private string $apiVersion = 'v1',
    ) {
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\QueryProviderInterface
     */
    #[\Override]
    public function provider(): Provider
    {
        return Provider::OpenAI;
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\QueryProviderInterface
     */
    #[\Override]
    public function run(Query $query): Response
    {
        $url = $this->transport->url($this->apiVersion, 'responses');

        try {
            $response = $this->transport->postRequest($url, [
                'json' => $query->request,
            ]);

            $record = $this->transport->decode($response, ResponsePayload::class);
        } finally {
            unset($query);
        }

        return new Response($this->provider(), $record->id, $record->completed, $record->text, $record->refusal, $record->error?->message);
    }
}
