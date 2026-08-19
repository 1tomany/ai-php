<?php

namespace OneToMany\AI\Contract\Resource;

use OneToMany\AI\Model;
use OneToMany\AI\Resource\File\LocalFile;
use OneToMany\AI\Resource\File\RemoteFile;

interface FilesInterface
{
    public function upload(Model $model, LocalFile $file): RemoteFile;

    public function delete(RemoteFile $file): void;
}
