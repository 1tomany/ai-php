<?php

namespace OneToMany\AI\Bridge;

use OneToMany\AI\File\LocalFile;
use OneToMany\AI\File\RemoteFile;
use OneToMany\AI\Provider;

interface FilesProviderInterface
{
    public function provider(): Provider;

    public function upload(LocalFile $file): RemoteFile;

    public function delete(RemoteFile $file): void;
}
