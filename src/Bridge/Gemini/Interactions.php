<?php

namespace OneToMany\AI\Bridge\Gemini;

use OneToMany\AI\Bridge\Gemini\Normalizer\QueryRequestNormalizer;
use OneToMany\AI\Bridge\Gemini\Payload\Interaction;
use OneToMany\AI\Bridge\Gemini\Payload\Usage as UsagePayload;
use OneToMany\AI\Bridge\QueryRequest;
use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Bridge\QueryProviderInterface;
use OneToMany\AI\Contract\Exception\ExceptionInterface;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Model;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\Queries\Prompt;
use OneToMany\AI\Resource\Queries\Query;
use OneToMany\AI\Resource\Queries\Response;
use OneToMany\AI\Resource\Queries\Usage;

use function implode;
use function trim;

final readonly class Interactions implements QueryProviderInterface
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
     * @param array<string, mixed> $options
     *
     * @throws ExceptionInterface when request normalization throws a package exception
     * @throws RuntimeException when compiling the query fails
     */
    #[\Override]
    public function compile(Model $model, Prompt $prompt, array $options = []): Query
    {
        try {
            $payload = $this->normalizer->normalize(new QueryRequest($model, $prompt, $options), 'json');
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException('Compiling the Gemini query failed.', previous: $e);
        }

        return new Query($model, $payload);
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\QueryProviderInterface
     *
     * @throws ExceptionInterface when interaction creation throws a package exception
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
            throw new RuntimeException('Running the Gemini query failed.', previous: $e);
        }
    }

    /**
     * @throws ExceptionInterface when interaction creation throws a package exception
     */
    private function runQuery(Query $query): Response
    {
        $payload = $this->transport->requestObject(
            'POST',
            $this->transport->url($this->apiVersion, 'interactions'),
            Interaction::class,
            ['json' => $query->payload],
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
}
