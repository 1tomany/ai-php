<?php

namespace OneToMany\AI\Inference;

use OneToMany\AI\Bridge\InferenceProviderInterface;
use OneToMany\AI\Bridge\InferenceRequest;
use OneToMany\AI\Contract\Exception\ExceptionInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Exception\LogicException;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\File\RemoteFile;
use OneToMany\AI\Model;
use OneToMany\AI\Provider;

use function sprintf;

final readonly class Inference implements InferenceInterface
{
    /**
     * @var array<string, InferenceProviderInterface>
     */
    private array $providers;

    /**
     * @param iterable<InferenceProviderInterface> $providers
     */
    public function __construct(
        iterable $providers,
    ) {
        $indexed = [];

        try {
            foreach ($providers as $provider) {
                $key = $provider->provider()->getValue();

                if (isset($indexed[$key])) {
                    throw new LogicException(sprintf('More than one "%s" inference provider is registered.', $key));
                }

                $indexed[$key] = $provider;
            }
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new LogicException('Registering the inference providers failed.', previous: $e);
        }

        $this->providers = $indexed;
    }

    /**
     * @param array<string, mixed> $options
     */
    #[\Override]
    public function create(Model $model, Prompt $prompt, array $options = []): Response
    {
        foreach ($prompt->input as $input) {
            if ($input instanceof RemoteFile && $model->provider !== $input->provider) {
                throw new InvalidArgumentException(sprintf('A "%s" file cannot be sent to a "%s" model.', $input->provider->getValue(), $model->provider->getValue()));
            }
        }

        try {
            $response = $this->provider($model->provider)->create(new InferenceRequest($model, $prompt, $options));
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Running inference with %s failed.', $model->provider->getName()), previous: $e);
        }

        if ($model->provider !== $response->provider) {
            throw new RuntimeException(sprintf('The "%s" inference provider returned a "%s" response.', $model->provider->getValue(), $response->provider->getValue()));
        }

        return $response;
    }

    private function provider(Provider $provider): InferenceProviderInterface
    {
        return $this->providers[$provider->getValue()]
            ?? throw new LogicException(sprintf('No "%s" inference provider is registered.', $provider->getValue()));
    }
}
