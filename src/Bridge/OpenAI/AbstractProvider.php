<?php

namespace OneToMany\AI\Bridge\OpenAI;

use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Bridge\ProviderInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Vendor;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function sprintf;

abstract readonly class AbstractProvider implements ProviderInterface
{
    public const string BASE_URL = 'https://api.openai.com';

    /**
     * @throws InvalidArgumentException when the API key is empty
     */
    public function __construct(
        protected Transport $transport,
        protected NormalizerInterface $normalizer,
        #[\SensitiveParameter] protected string $apiKey,
        protected string $apiVersion = 'v1',
    ) {
        if ('' === $this->apiKey) {
            throw new InvalidArgumentException(sprintf('The %s API key cannot be empty.', $this->vendor()->getName()));
        }
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\ProviderInterface
     */
    #[\Override]
    public function vendor(): Vendor
    {
        return Vendor::OpenAI;
    }

    protected function url(string ...$parts): string
    {
        return $this->transport->url(self::BASE_URL, $this->apiVersion, ...$parts);
    }
}
