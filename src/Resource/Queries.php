<?php

namespace OneToMany\AI\Resource;

use OneToMany\AI\Contract\Bridge\QueryProviderInterface;
use OneToMany\AI\Contract\Exception\ExceptionInterface;
use OneToMany\AI\Contract\Resource\QueriesInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Exception\LogicException;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Model;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\Files\RemoteFile;
use OneToMany\AI\Resource\Queries\Prompt;
use OneToMany\AI\Resource\Queries\Query;
use OneToMany\AI\Resource\Queries\Response;

use function sprintf;

final readonly class Queries implements QueriesInterface
{
    /**
     * @var array<string, QueryProviderInterface>
     */
    private array $providers;

    /**
     * @param iterable<QueryProviderInterface> $providers
     *
     * @throws ExceptionInterface when a provider throws a package exception during registration
     * @throws LogicException when the same provider is registered more than once
     * @throws LogicException when registering a provider fails
     */
    public function __construct(
        iterable $providers,
    ) {
        $indexedProviders = [];

        foreach ($providers as $provider) {
            if (isset($indexedProviders[$provider->provider()->getValue()])) {
                throw new LogicException(sprintf('The "%s" query provider is already registered.', $provider->provider()->getValue()));
            }

            $indexedProviders[$provider->provider()->getValue()] = $provider;
        }

        $this->providers = $indexedProviders;
    }

    /**
     * @see OneToMany\AI\Contract\Resource\QueriesInterface
     *
     * @param array<string, mixed> $options
     *
     * @throws ExceptionInterface when the selected provider throws a package exception
     * @throws InvalidArgumentException when a file and model belong to different providers
     * @throws LogicException when no provider is registered for the model
     * @throws RuntimeException when the selected provider throws a foreign exception
     * @throws RuntimeException when the provider compiles a query for another model
     */
    #[\Override]
    public function compile(Model $model, Prompt $prompt, array $options = []): Query
    {
        foreach ($prompt->input as $input) {
            if ($input instanceof RemoteFile && $model->provider !== $input->provider) {
                throw new InvalidArgumentException(sprintf('A "%s" file cannot be sent to a "%s" model.', $input->provider->getValue(), $model->provider->getValue()));
            }
        }

        try {
            $query = $this->provider($model->provider)->compile($model, $prompt, $options);
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Compiling a query for %s failed.', $model->provider->getName()), previous: $e);
        }

        if ($model->provider !== $query->model->provider || $model->name !== $query->model->name) {
            throw new RuntimeException(sprintf('The "%s" query provider compiled a query for "%s" instead of "%s".', $model->provider->getValue(), (string) $query->model, (string) $model));
        }

        return $query;
    }

    /**
     * @see OneToMany\AI\Contract\Resource\QueriesInterface
     *
     * @throws ExceptionInterface when the selected provider throws a package exception
     * @throws LogicException when no provider is registered for the query
     * @throws RuntimeException when the selected provider throws a foreign exception
     * @throws RuntimeException when the provider returns a response for another provider
     */
    #[\Override]
    public function run(Query $query): Response
    {
        try {
            $response = $this->provider($query->getProvider())->run($query);
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Running the %s query failed.', $query->getProvider()->getName()), previous: $e);
        }

        if ($query->getProvider() !== $response->provider) {
            throw new RuntimeException(sprintf('The "%s" query provider returned a "%s" response.', $query->getProvider()->getValue(), $response->provider->getValue()));
        }

        return $response;
    }

    /**
     * @see OneToMany\AI\Contract\Resource\QueriesInterface
     *
     * @param array<string, mixed> $options
     *
     * @throws ExceptionInterface when compiling or running the query throws a package exception
     */
    #[\Override]
    public function compileAndRun(Model $model, Prompt $prompt, array $options = []): Response
    {
        return $this->run($this->compile($model, $prompt, $options));
    }

    /**
     * @throws InvalidArgumentException when the query provider is not registered
     */
    private function provider(Provider $provider): QueryProviderInterface
    {
        if (!isset($this->providers[$provider->getValue()])) {
            throw new InvalidArgumentException(sprintf('The "%s" query provider is not registered.', $provider->getValue()));
        }

        return $this->providers[$provider->getValue()];
    }
}
