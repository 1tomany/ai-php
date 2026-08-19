<?php

namespace OneToMany\AI\Bridge\OpenAI;

use OneToMany\AI\Bridge\OpenAI\Normalizer\QueryRequestNormalizer;
use OneToMany\AI\Bridge\OpenAI\Responses\Responses\Enum\ResponseStatus;
use OneToMany\AI\Bridge\OpenAI\Responses\Responses\Response as ResponsePayload;
use OneToMany\AI\Bridge\QueryRequest;
use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Bridge\QueryProviderInterface;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Model;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\Query\Prompt;
use OneToMany\AI\Resource\Query\Query;
use OneToMany\AI\Resource\Query\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;

use function sprintf;

final readonly class QueryProvider implements QueryProviderInterface
{
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
        $url = $this->transport->url($this->apiVersion, 'responses');

        $response = $this->transport->postRequest($url, [
            'json' => $query->request,
        ]);

        $record = $this->transport->decode($response, ResponsePayload::class);

        /*
        $texts = [];
        $refusal = null;

        foreach ($payload->output as $output) {
            foreach ($output->content as $part) {
                if ('output_text' === $part->type && null !== $part->text && '' !== $part->text) {
                    $texts[] = $part->text;
                }

                if ('refusal' === $part->type && null !== $part->refusal) {
                    $refusal = trim($part->refusal) ?: null;
                }
            }
        }

        $usage = $payload->usage ?? new UsagePayload();
        $error = null;

        if (null !== $payload->error && null !== $payload->error->message) {
            $error = trim($payload->error->message) ?: null;
        }

        if (null === $error && null !== $payload->incomplete_details && null !== $payload->incomplete_details->reason) {
            $error = trim($payload->incomplete_details->reason) ?: null;
        }
        */

        print_r($record);

        return new Response($this->provider(), $record->id, ($record->status ?? ResponseStatus::Completed)->value, $record->getText(), null, $record->getError());

        /*
            provider: Provider::OpenAI,
            id: $payload->id,
            status: $payload->status,
            text: [] !== $texts ? implode("\n", $texts) : null,
            refusal: $refusal,
            error: $error,
            usage: new Usage(
                inputTokens: $usage->input_tokens,
                outputTokens: $usage->output_tokens,
                cachedInputTokens: $usage->input_token_details->cached_tokens,
                reasoningTokens: $usage->output_token_details->reasoning_tokens,
                totalTokens: $usage->total_tokens,
            ),
        );
        */
    }
}
