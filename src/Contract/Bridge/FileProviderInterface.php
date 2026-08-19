<?php

namespace OneToMany\AI\Contract\Bridge;

use OneToMany\AI\Resource\File\LocalFile;
use OneToMany\AI\Resource\File\RemoteFile;

interface FileProviderInterface extends ProviderInterface
{
    public function upload(LocalFile $file): RemoteFile;

    public function delete(RemoteFile $file): void;
}
