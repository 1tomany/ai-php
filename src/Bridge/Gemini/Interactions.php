<?php

namespace OneToMany\AI\Bridge\Gemini;

use OneToMany\AI\Bridge\Gemini\Normalizer\InferenceRequestNormalizer;
use OneToMany\AI\Bridge\Gemini\Payload\Interaction;
use OneToMany\AI\Bridge\Gemini\Payload\Usage as UsagePayload;
use OneToMany\AI\Bridge\InferenceRequest;
use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Bridge\InferenceProviderInterface;
use OneToMany\AI\Contract\Exception\ExceptionInterface;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\Inference\Response;
use OneToMany\AI\Resource\Inference\Usage;

use function implode;
use function trim;

final readonly class Interactions implements InferenceProviderInterface
{
    public function __construct(
        private Transport $transport,
        private InferenceRequestNormalizer $inferenceNormalizer,
        private string $apiVersion = 'v1beta',
    ) {
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\InferenceProviderInterface
     */
    #[\Override]
    public function provider(): Provider
    {
        return Provider::Gemini;
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\InferenceProviderInterface
     *
     * @throws RuntimeException when creating the interaction fails
     */
    #[\Override]
    public function create(InferenceRequest $request): Response
    {
        try {
            return $this->createResponse($request);
        } catch (\Throwable $e) {
            throw new RuntimeException('Creating the Gemini interaction failed.', previous: $e);
        }
    }

    /**
     * @throws ExceptionInterface when request normalization throws a package exception
     * @throws RuntimeException when normalizing the request fails
     */
    private function createResponse(InferenceRequest $request): Response
    {
        try {
            $requestPayload = $this->inferenceNormalizer->normalize($request, 'json');
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException('Normalizing the Gemini inference request failed.', previous: $e);
        }

        $payload = $this->transport->requestObject(
            'POST',
            $this->transport->url($this->apiVersion, 'interactions'),
            Interaction::class,
            ['json' => $requestPayload],
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
