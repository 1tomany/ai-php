<?php

namespace OneToMany\AI\Contract\Resource;

use OneToMany\AI\Resource\File\LocalFile;
use OneToMany\AI\Resource\File\RemoteFile;
use OneToMany\AI\Vendor;

interface FilesInterface
{
    public function upload(string|Vendor $vendor, string|LocalFile $file): RemoteFile;

    public function delete(RemoteFile $file): void;
}
