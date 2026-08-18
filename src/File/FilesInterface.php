<?php

namespace OneToMany\AI\File;

use OneToMany\AI\Model;

interface FilesInterface
{
    public function upload(Model $model, LocalFile $file): RemoteFile;

    public function delete(RemoteFile $file): void;
}
