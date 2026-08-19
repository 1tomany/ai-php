<?php

namespace OneToMany\AI\Contract\Bridge;

use OneToMany\AI\Provider;
use OneToMany\AI\Resource\File\LocalFile;
use OneToMany\AI\Resource\File\RemoteFile;

interface FileProviderInterface
{
    public function provider(): Provider;

    public function upload(LocalFile $file): RemoteFile;

    public function delete(RemoteFile $file): void;
}
