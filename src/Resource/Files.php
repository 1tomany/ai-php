<?php

namespace OneToMany\AI\Resource;

use OneToMany\AI\Contract\Bridge\FilesProviderInterface;
use OneToMany\AI\Contract\Resource\FilesInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Model;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\File\LocalFile;
use OneToMany\AI\Resource\File\RemoteFile;

use function sprintf;

final readonly class Files implements FilesInterface
{
    /**
     * @var array<string, FilesProviderInterface>
     */
    private array $providers;

    /**
     * @param iterable<FilesProviderInterface> $providers
     *
     * @throws InvalidArgumentException when a file provider is already registered
     */
    public function __construct(
        iterable $providers,
    ) {
        $indexedProviders = [];

        foreach ($providers as $provider) {
            if (isset($indexedProviders[$provider->provider()->getValue()])) {
                throw new InvalidArgumentException(sprintf('The "%s" file provider is already registered.', $provider->provider()->getValue()));
            }

            $indexedProviders[$provider->provider()->getValue()] = $provider;
        }

        $this->providers = $indexedProviders;
    }

    /**
     * @see OneToMany\AI\Contract\Resource\FilesInterface
     */
    #[\Override]
    public function upload(Model $model, LocalFile $file): RemoteFile
    {
        return $this->getProvider($model->provider)->upload($file);
    }

    /**
     * @see OneToMany\AI\Contract\Resource\FilesInterface
     */
    #[\Override]
    public function delete(RemoteFile $file): void
    {
        $this->getProvider($file->provider)->delete($file);
    }

    /**
     * @throws InvalidArgumentException when the file provider is not registered
     */
    private function getProvider(Provider $provider): FilesProviderInterface
    {
        if (!isset($this->providers[$provider->getValue()])) {
            throw new InvalidArgumentException(sprintf('The "%s" file provider is not registered.', $provider->getValue()));
        }

        return $this->providers[$provider->getValue()];
    }
}
