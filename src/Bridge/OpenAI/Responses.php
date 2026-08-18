<?php

namespace OneToMany\AI\Bridge\OpenAI;

use OneToMany\AI\Bridge\InferenceRequest;
use OneToMany\AI\Bridge\OpenAI\Payload\Response as ResponsePayload;
use OneToMany\AI\Bridge\OpenAI\Payload\Usage as UsagePayload;
use OneToMany\AI\Contract\Bridge\InferenceProviderInterface;
use OneToMany\AI\Contract\Exception\ExceptionInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\Inference\Response;
use OneToMany\AI\Resource\Inference\Usage;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function implode;
use function is_array;
use function trim;

final readonly class Responses implements InferenceProviderInterface
{
    public function __construct(
        private Transport $transport,
        private NormalizerInterface $normalizer,
    ) {
    }

    #[\Override]
    public function provider(): Provider
    {
        return Provider::OpenAI;
    }

    #[\Override]
    public function create(InferenceRequest $request): Response
    {
        if (Provider::OpenAI !== $request->model->provider) {
            throw new InvalidArgumentException('The OpenAI responses provider requires an OpenAI model.');
        }

        try {
            return $this->createResponse($request);
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException('Creating the OpenAI response failed.', previous: $e);
        }
    }

    private function createResponse(InferenceRequest $request): Response
    {
        try {
            $requestPayload = $this->normalizer->normalize($request, 'json');
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException('Normalizing the OpenAI inference request failed.', previous: $e);
        }

        if (!is_array($requestPayload)) {
            throw new RuntimeException('Normalizing the OpenAI inference request did not produce an object.');
        }

        $decoded = $this->transport->requestObject(
            'POST',
            $this->transport->url('responses'),
            ResponsePayload::class,
            ['json' => $requestPayload],
        );
        $payload = $decoded->payload;

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
        $cachedInputTokens = null !== $usage->inputTokenDetails ? $usage->inputTokenDetails->cachedTokens : 0;
        $reasoningTokens = null !== $usage->outputTokenDetails ? $usage->outputTokenDetails->reasoningTokens : 0;
        $error = null;

        if (null !== $payload->error && null !== $payload->error->message) {
            $error = trim($payload->error->message) ?: null;
        }

        if (null === $error && null !== $payload->incompleteDetails && null !== $payload->incompleteDetails->reason) {
            $error = trim($payload->incompleteDetails->reason) ?: null;
        }

        return new Response(
            provider: Provider::OpenAI,
            id: $payload->id,
            status: $payload->status,
            text: [] !== $texts ? implode("\n", $texts) : null,
            refusal: $refusal,
            error: $error,
            usage: new Usage(
                inputTokens: $usage->inputTokens,
                outputTokens: $usage->outputTokens,
                cachedInputTokens: $cachedInputTokens,
                reasoningTokens: $reasoningTokens,
                totalTokens: $usage->totalTokens,
            ),
            raw: $decoded->raw,
        );
    }
}
