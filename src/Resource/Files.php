<?php

namespace OneToMany\AI\Resource;

use OneToMany\AI\Contract\Bridge\FilesProviderInterface;
use OneToMany\AI\Contract\Exception\ExceptionInterface;
use OneToMany\AI\Contract\Resource\FilesInterface;
use OneToMany\AI\Exception\LogicException;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Model;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\Files\LocalFile;
use OneToMany\AI\Resource\Files\RemoteFile;

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
     * @throws ExceptionInterface when a provider throws a package exception during registration
     * @throws LogicException when the same provider is registered more than once
     * @throws LogicException when registering a provider fails
     */
    public function __construct(
        iterable $providers,
    ) {
        $indexed = [];

        try {
            foreach ($providers as $provider) {
                $key = $provider->provider()->getValue();

                if (isset($indexed[$key])) {
                    throw new LogicException(sprintf('More than one "%s" files provider is registered.', $key));
                }

                $indexed[$key] = $provider;
            }
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new LogicException('Registering the files providers failed.', previous: $e);
        }

        $this->providers = $indexed;
    }

    /**
     * @see OneToMany\AI\Contract\Resource\FilesInterface
     *
     * @throws ExceptionInterface when the selected provider throws a package exception
     * @throws LogicException when no provider is registered for the model
     * @throws RuntimeException when the selected provider throws a foreign exception
     * @throws RuntimeException when the provider returns a file for another provider
     */
    #[\Override]
    public function upload(Model $model, LocalFile $file): RemoteFile
    {
        try {
            $remoteFile = $this->provider($model->provider)->upload($file);
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Uploading the file to %s failed.', $model->provider->getName()), previous: $e);
        }

        if ($model->provider !== $remoteFile->provider) {
            throw new RuntimeException(sprintf('The "%s" files provider returned a "%s" file.', $model->provider->getValue(), $remoteFile->provider->getValue()));
        }

        return $remoteFile;
    }

    /**
     * @see OneToMany\AI\Contract\Resource\FilesInterface
     *
     * @throws ExceptionInterface when the selected provider throws a package exception
     * @throws LogicException when no provider is registered for the file
     * @throws RuntimeException when the selected provider throws a foreign exception
     */
    #[\Override]
    public function delete(RemoteFile $file): void
    {
        try {
            $this->provider($file->provider)->delete($file);
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Deleting the %s file failed.', $file->provider->getName()), previous: $e);
        }
    }

    /**
     * @throws LogicException when no files provider is registered
     */
    private function provider(Provider $provider): FilesProviderInterface
    {
        return $this->providers[$provider->getValue()]
            ?? throw new LogicException(sprintf('No "%s" files provider is registered.', $provider->getValue()));
    }
}
