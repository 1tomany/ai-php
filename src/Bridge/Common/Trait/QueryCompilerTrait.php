<?php

namespace OneToMany\AI\Bridge\Common\Trait;

use OneToMany\AI\Bridge\QueryRequest;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Model;
use OneToMany\AI\Resource\Query\Prompt;
use OneToMany\AI\Resource\Query\Query;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;

trait QueryCompilerTrait
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
            $request = $this->normalizer->normalize(new QueryRequest($model, $prompt, $options));
        } catch (SerializerExceptionInterface $e) {
            throw new RuntimeException(sprintf('Compiling the %s query failed.', $this->provider()->getName()), previous: $e);
        }

        return new Query($model, $request);
    }
}
