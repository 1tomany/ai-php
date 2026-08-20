<?php

namespace OneToMany\AI\Contract\Bridge;

use OneToMany\AI\Resource\File\LocalFile;
use OneToMany\AI\Resource\File\RemoteFile;

interface FileProviderInterface extends ProviderInterface
{
    public function upload(LocalFile $file): RemoteFile;

    /**
     * @param non-empty-string $fileId
     */
    public function delete(string $fileId): void;
}
