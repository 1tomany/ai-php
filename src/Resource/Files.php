<?php

namespace OneToMany\AI\Resource;

use OneToMany\AI\Contract\Bridge\FileProviderInterface;
use OneToMany\AI\Contract\Resource\FilesInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Model;
use OneToMany\AI\Resource\File\LocalFile;
use OneToMany\AI\Resource\File\RemoteFile;

final readonly class Files implements FilesInterface
{
    /**
     * @var Registry<FileProviderInterface>
     */
    private Registry $providers;

    /**
     * @see OneToMany\AI\Resource\Registry::__construct()
     *
     * @param iterable<FileProviderInterface> $providers
     */
    public function __construct(
        iterable $providers,
    ) {
        $this->providers = new Registry($providers);
    }

    /**
     * @see OneToMany\AI\Contract\Resource\FilesInterface
     *
     * @throws InvalidArgumentException when the provider is not registered
     */
    #[\Override]
    public function upload(Model $model, LocalFile $file): RemoteFile
    {
        return $this->providers->get($model->provider)->upload($file);
    }

    /**
     * @see OneToMany\AI\Contract\Resource\FilesInterface
     *
     * @throws InvalidArgumentException when the provider is not registered
     */
    #[\Override]
    public function delete(RemoteFile $file): void
    {
        $this->providers->get($file->provider)->delete($file);
    }
}
