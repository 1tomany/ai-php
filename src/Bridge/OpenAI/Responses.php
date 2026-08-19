<?php

namespace OneToMany\AI\Bridge\OpenAI;

use OneToMany\AI\Bridge\OpenAI\Normalizer\QueryRequestNormalizer;
use OneToMany\AI\Bridge\OpenAI\Payload\Response as ResponsePayload;
use OneToMany\AI\Bridge\OpenAI\Payload\Usage as UsagePayload;
use OneToMany\AI\Bridge\QueryRequest;
use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Bridge\QueryProviderInterface;
use OneToMany\AI\Contract\Exception\ExceptionInterface;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Model;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\Query\Prompt;
use OneToMany\AI\Resource\Query\Query;
use OneToMany\AI\Resource\Query\Response;
use OneToMany\AI\Resource\Query\Usage;

use function implode;
use function trim;

final readonly class Responses implements QueryProviderInterface
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
     * @param array<string, mixed> $options
     *
     * @throws ExceptionInterface when request normalization throws a package exception
     * @throws RuntimeException when compiling the query fails
     */
    #[\Override]
    public function compile(Model $model, Prompt $prompt, array $options = []): Query
    {
        try {
            $payload = $this->normalizer->normalize(new QueryRequest($model, $prompt, $options));
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException('Compiling the OpenAI query failed.', previous: $e);
        }

        return new Query($model, $payload);
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\QueryProviderInterface
     *
     * @throws ExceptionInterface when response creation throws a package exception
     * @throws RuntimeException when running the query fails
     */
    #[\Override]
    public function run(Query $query): Response
    {
        try {
            return $this->runQuery($query);
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException('Running the OpenAI query failed.', previous: $e);
        }
    }

    /**
     * @throws ExceptionInterface when response creation throws a package exception
     */
    private function runQuery(Query $query): Response
    {
        $url = $this->transport->url($this->apiVersion, 'responses');

        $payload = $this->transport->requestObject('POST', $url, ResponsePayload::class, [
            'json' => $query->payload,
        ]);

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

        return new Response(
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
    }
}
