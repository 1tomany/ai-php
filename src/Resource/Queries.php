<?php

namespace OneToMany\AI\Resource;

use OneToMany\AI\Contract\Bridge\QueryProviderInterface;
use OneToMany\AI\Contract\Resource\QueriesInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Model;
use OneToMany\AI\Resource\Query\Prompt;
use OneToMany\AI\Resource\Query\Query;
use OneToMany\AI\Resource\Query\Response;


final readonly class Queries implements QueriesInterface
{
    /**
     * @var Registry<QueryProviderInterface>
     */
    private Registry $providers;

    /**
     * @see OneToMany\AI\Resource\Registry::__construct()
     *
     * @param iterable<QueryProviderInterface> $providers
     */
    public function __construct(
        iterable $providers,
    ) {
        $this->providers = new Registry($providers);
    }

    /**
     * @see OneToMany\AI\Contract\Resource\QueriesInterface
     */
    #[\Override]
    public function compile(Model $model, Prompt $prompt, array $options = []): Query
    {
        return $this->providers->get($model->provider)->compile($model, $prompt, $options);
    }

    /**
     * @see OneToMany\AI\Contract\Resource\QueriesInterface
     */
    #[\Override]
    public function run(Query $query): Response
    {
        return $this->providers->get($query->provider())->run($query);
    }

    /**
     * @see OneToMany\AI\Contract\Resource\QueriesInterface
     *
     * @param array<string, mixed> $options
     */
    #[\Override]
    public function compileAndRun(Model $model, Prompt $prompt, array $options = []): Response
    {
        return $this->run($this->compile($model, $prompt, $options));
    }
}
