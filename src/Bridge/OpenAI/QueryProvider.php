<?php

namespace OneToMany\AI\Bridge\OpenAI;

use OneToMany\AI\Bridge\OpenAI\Normalizer\QueryRequestNormalizer;
use OneToMany\AI\Bridge\OpenAI\Responses\Responses\Response as ResponsePayload;
use OneToMany\AI\Bridge\QueryCompilerTrait;
use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Bridge\QueryProviderInterface;
use OneToMany\AI\Resource\Query\Query;
use OneToMany\AI\Resource\Query\Response;

final readonly class QueryProvider extends AbstractProvider implements QueryProviderInterface
{
    use QueryCompilerTrait;

    /**
     * @see OneToMany\AI\Bridge\OpenAI\AbstractProvider::__construct()
     */
    public function __construct(
        Transport $transport,
        #[\SensitiveParameter]
        string $apiKey,
        private QueryRequestNormalizer $normalizer,
        string $apiVersion = 'v1',
    ) {
        parent::__construct($transport, $apiKey, $apiVersion);
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\QueryProviderInterface
     */
    #[\Override]
    public function run(Query $query): Response
    {
        $url = $this->url($this->apiVersion, 'responses');

        try {
            $response = $this->postRequest($url, [
                'json' => $query->request,
            ]);

            $record = $this->transport->decode($response, ResponsePayload::class);
        } finally {
            unset($query);
        }

        return new Response($this->provider(), $record->id, $record->completed, $record->text, $record->refusal, $record->error?->message);
    }
}
