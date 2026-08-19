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
     *
     * @throws InvalidArgumentException when the prompt has no input
     */
    #[\Override]
    public function compile(Model $model, Prompt $prompt, array $options = []): Query
    {
        if ($prompt->isEmpty()) {
            throw new InvalidArgumentException('At least one text or file input is required to compile a prompt into a query.');
        }

        return $this->providers->get($model->provider)->compile($model, $prompt, $options);
    }

    /**
     * @see OneToMany\AI\Contract\Resource\QueriesInterface
     */
    #[\Override]
    public function run(Query $query): Response
    {
        return $this->providers->get($query->model->provider)->run($query);
    }

    /**
     * @see OneToMany\AI\Contract\Resource\QueriesInterface
     * @see OneToMany\AI\Resource\Queries::compile()
     * @see OneToMany\AI\Resource\Queries::run()
     *
     * @param array<string, mixed> $options
     */
    #[\Override]
    public function compileAndRun(string|Model $model, Prompt $prompt, array $options = []): Response
    {
        if (!$model instanceof Model) {
            $model = Model::create($model);
        }

        return $this->run($this->compile($model, $prompt, $options));
    }
}
