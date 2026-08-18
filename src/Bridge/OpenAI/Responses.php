<?php

namespace OneToMany\AI\Bridge\OpenAI;

use OneToMany\AI\Bridge\InferenceRequest;
use OneToMany\AI\Bridge\OpenAI\Normalizer\InferenceRequestNormalizer;
use OneToMany\AI\Bridge\OpenAI\Payload\Response as ResponsePayload;
use OneToMany\AI\Bridge\OpenAI\Payload\Usage as UsagePayload;
use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Bridge\InferenceProviderInterface;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\Inference\Response;
use OneToMany\AI\Resource\Inference\Usage;

use function implode;
use function trim;

final readonly class Responses implements InferenceProviderInterface
{
    public function __construct(
        private Transport $transport,
        private InferenceRequestNormalizer $normalizer,
        private string $apiVersion = 'v1',
    ) {
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\InferenceProviderInterface
     */
    #[\Override]
    public function provider(): Provider
    {
        return Provider::OpenAI;
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\InferenceProviderInterface
     *
     * @throws RuntimeException when creating the response fails
     */
    #[\Override]
    public function create(InferenceRequest $request): Response
    {
        try {
            return $this->createResponse($request);
        } catch (\Throwable $e) {
            throw new RuntimeException('Creating the OpenAI response failed.', previous: $e);
        }
    }

    /**
     * @throws RuntimeException when normalizing the request fails
     */
    private function createResponse(InferenceRequest $request): Response
    {
        try {
            $requestPayload = $this->normalizer->normalize($request);
        } catch (\Throwable $e) {
            throw new RuntimeException('Normalizing the OpenAI inference request failed.', previous: $e);
        }

        $url = $this->transport->url($this->apiVersion, 'responses');

        $payload = $this->transport->requestObject('POST', $url, ResponsePayload::class, [
            'json' => $requestPayload,
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
        $cachedInputTokens = $usage->input_token_details->cached_tokens;
        $reasoningTokens = $usage->output_token_details->reasoning_tokens;
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
                cachedInputTokens: $cachedInputTokens,
                reasoningTokens: $reasoningTokens,
                totalTokens: $usage->total_tokens,
            ),
        );
    }
}
