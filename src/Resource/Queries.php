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
    public function compile(string|Model $model, Prompt $prompt, array $options = []): Query
    {
        $model = Model::create($model);

        if ($prompt->isEmpty()) {
            throw new InvalidArgumentException('At least one text or file input is required to compile a prompt into a query.');
        }

        return $this->providers->get($model->vendor)->compile($model, $prompt, $options);
    }

    /**
     * @see OneToMany\AI\Contract\Resource\QueriesInterface
     */
    #[\Override]
    public function run(Query $query): Response
    {
        return $this->providers->get($query->model->vendor)->run($query);
    }

    /**
     * @see OneToMany\AI\Contract\Resource\QueriesInterface
     *
     * @param array<string, mixed> $options
     */
    #[\Override]
    public function compileAndRun(string|Model $model, Prompt $prompt, array $options = []): Response
    {
        return $this->run($this->compile(Model::create($model), $prompt, $options));
    }
}
