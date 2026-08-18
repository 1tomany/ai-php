<?php

namespace OneToMany\AI\Bridge\Gemini;

use OneToMany\AI\Bridge\Gemini\Payload\Interaction;
use OneToMany\AI\Bridge\Gemini\Payload\Usage as UsagePayload;
use OneToMany\AI\Bridge\InferenceProviderInterface;
use OneToMany\AI\Bridge\InferenceRequest;
use OneToMany\AI\Contract\Exception\ExceptionInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Inference\Response;
use OneToMany\AI\Inference\Usage;
use OneToMany\AI\Provider;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function implode;
use function is_array;
use function trim;

final readonly class Interactions implements InferenceProviderInterface
{
    public function __construct(
        private Transport $transport,
        private NormalizerInterface $normalizer,
        private string $apiVersion = 'v1beta',
    ) {
    }

    #[\Override]
    public function provider(): Provider
    {
        return Provider::Gemini;
    }

    #[\Override]
    public function create(InferenceRequest $request): Response
    {
        if (Provider::Gemini !== $request->model->provider) {
            throw new InvalidArgumentException('The Gemini interactions provider requires a Gemini model.');
        }

        try {
            return $this->createResponse($request);
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException('Creating the Gemini interaction failed.', previous: $e);
        }
    }

    private function createResponse(InferenceRequest $request): Response
    {
        try {
            $requestPayload = $this->normalizer->normalize($request, 'json');
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException('Normalizing the Gemini inference request failed.', previous: $e);
        }

        if (!is_array($requestPayload)) {
            throw new RuntimeException('Normalizing the Gemini inference request did not produce an object.');
        }

        $decoded = $this->transport->requestObject(
            'POST',
            $this->transport->url($this->apiVersion, 'interactions'),
            Interaction::class,
            ['json' => $requestPayload],
        );
        $payload = $decoded->payload;

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
                inputTokens: $usage->inputTokens,
                outputTokens: $usage->outputTokens,
                cachedInputTokens: $usage->cachedTokens,
                reasoningTokens: $usage->thoughtTokens,
                totalTokens: $usage->totalTokens,
            ),
            raw: $decoded->raw,
        );
    }
}
