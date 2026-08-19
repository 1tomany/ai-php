<?php

namespace OneToMany\AI\Bridge\Gemini;

use OneToMany\AI\Bridge\Common\Trait\QueryCompilerTrait;
use OneToMany\AI\Bridge\Gemini\Normalizer\QueryRequestNormalizer;
use OneToMany\AI\Bridge\Gemini\Responses\Interactions\Interaction as ResponsePayload;
use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Bridge\QueryProviderInterface;
use OneToMany\AI\Resource\Query\Query;
use OneToMany\AI\Resource\Query\Response;

final readonly class QueryProvider extends AbstractProvider implements QueryProviderInterface
{
    use QueryCompilerTrait;

    /**
     * @see OneToMany\AI\Bridge\Gemini\AbstractProvider::__construct()
     */
    public function __construct(
        Transport $transport,
        private QueryRequestNormalizer $normalizer,
        #[\SensitiveParameter] string $apiKey,
        string $apiVersion = 'v1beta',
    ) {
        parent::__construct($transport, $apiKey, $apiVersion);
    }

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

        return new Response($this->provider(), $record->id, $record->completed, $record->text, null, $record->error?->message);
    }
}
