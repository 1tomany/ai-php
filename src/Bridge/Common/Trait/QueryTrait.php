<?php

namespace OneToMany\AI\Bridge\Common\Trait;

use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Model;
use OneToMany\AI\Resource\Query\Prompt;
use OneToMany\AI\Resource\Query\Query;
use OneToMany\AI\Resource\Query\QueryDefinition;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;

trait QueryTrait
{
    /**
     * @see OneToMany\AI\Contract\Bridge\QueryProviderInterface
     *
     * @throws RuntimeException when compiling the query fails
     */
    #[\Override]
    public function compile(Model $model, Prompt $prompt, array $options = []): Query
    {
        try {
            /** @var array<string, mixed> $request */
            $request = $this->normalizer->normalize(new QueryDefinition($model, $prompt, $options));
        } catch (SerializerExceptionInterface $e) {
            throw new RuntimeException(sprintf('Compiling the %s query failed.', $this->vendor()->getName()), previous: $e);
        }

        return new Query($model, $request);
    }
}
