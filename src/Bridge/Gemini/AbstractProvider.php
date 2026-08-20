<?php

namespace OneToMany\AI\Bridge\Gemini;

use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Bridge\ProviderInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Vendor;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function sprintf;

abstract readonly class AbstractProvider implements ProviderInterface
{
    public const string BASE_URL = 'https://generativelanguage.googleapis.com';

    /**
     * @throws InvalidArgumentException when the API key is empty
     */
    public function __construct(
        protected Transport $transport,
        protected NormalizerInterface $normalizer,
        #[\SensitiveParameter] protected string $apiKey,
        protected string $apiVersion = 'v1beta',
    ) {
        if ('' === $this->apiKey) {
            throw new InvalidArgumentException(sprintf('The %s API key cannot be empty.', $this->getVendor()->getName()));
        }
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\ProviderInterface
     */
    #[\Override]
    public static function getVendor(): Vendor
    {
        return Vendor::Gemini;
    }

    /**
     * @return non-empty-string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    protected function url(string ...$parts): string
    {
        return $this->transport->url(self::BASE_URL, ...$parts);
    }
}
