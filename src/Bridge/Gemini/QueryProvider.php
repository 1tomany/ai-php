<?php

namespace OneToMany\AI\Bridge\Gemini;

use OneToMany\AI\Bridge\Gemini\Normalizer\QueryRequestNormalizer;
use OneToMany\AI\Bridge\Gemini\Responses\Interactions\Interaction;
use OneToMany\AI\Bridge\Gemini\Responses\Interactions\Usage as UsagePayload;
use OneToMany\AI\Bridge\QueryRequest;
use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Bridge\QueryProviderInterface;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Model;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\Query\Prompt;
use OneToMany\AI\Resource\Query\Query;
use OneToMany\AI\Resource\Query\Response;
use OneToMany\AI\Resource\Query\Usage;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;

use function implode;
use function trim;

final readonly class QueryProvider implements QueryProviderInterface
{
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
     *
     * @throws RuntimeException when compiling the query fails
     */
    #[\Override]
    public function compile(Model $model, Prompt $prompt, array $options = []): Query
    {
        try {
            $request = $this->normalizer->normalize(new QueryRequest($model, $prompt, $options));
        } catch (SerializerExceptionInterface $e) {
            throw new RuntimeException(sprintf('Compiling the %s query failed.', $this->provider()->getName()), previous: $e);
        }

        return new Query($model, $request);
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\QueryProviderInterface
     */
    #[\Override]
    public function run(Query $query): Response
    {
        $url = $this->transport->url($this->apiVersion, 'interactions');

        $response = $this->transport->postRequest($url, [
            'json' => $query->request,
        ]);

        $response->toArray(false);

        throw new RuntimeException('Not implemented!');
    }

    /*
    private function runQuery(Query $query): Response
    {
        $payload = $this->transport->requestObject(
            'POST',
            $this->transport->url($this->apiVersion, 'interactions'),
            Interaction::class,
            ['json' => $query->request],
        );

        $texts = [];

        foreach ($payload->steps as $step) {
            if ('model_output' !== $step->type) {
                continue;
            }

            foreach ($step->content as $content) {
                if ('text' === $content->type && null !== $content->text && '' !== $content->text) {
                    $texts[] = $content->text;
                }
            }
        }

        $usage = $payload->usage ?? new UsagePayload();
        $error = null;

        foreach ($payload->errors as $payloadError) {
            if (null === $payloadError->message) {
                continue;
            }

            if ('' !== $message = trim($payloadError->message)) {
                $error = $message;

                break;
            }
        }

        return new Response(
            provider: Provider::Gemini,
            id: $payload->id,
            status: $payload->status,
            text: [] !== $texts ? implode("\n", $texts) : null,
            refusal: null,
            error: $error,
            usage: new Usage(
                inputTokens: $usage->total_input_tokens,
                outputTokens: $usage->total_output_tokens,
                cachedInputTokens: $usage->total_cached_tokens,
                reasoningTokens: $usage->total_thought_tokens,
                totalTokens: $usage->total_tokens,
            ),
        );
    }
    */
}
