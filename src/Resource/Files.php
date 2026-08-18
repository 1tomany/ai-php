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

    private function provider(Provider $provider): FilesProviderInterface
    {
        return $this->providers[$provider->getValue()]
            ?? throw new LogicException(sprintf('No "%s" files provider is registered.', $provider->getValue()));
    }
}
