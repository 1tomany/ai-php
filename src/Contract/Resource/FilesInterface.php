<?php

namespace OneToMany\AI\Contract\Resource;

use OneToMany\AI\Provider;
use OneToMany\AI\Resource\File\LocalFile;
use OneToMany\AI\Resource\File\RemoteFile;

interface FilesInterface
{
    /**
     * @see OneToMany\AI\Provider::create()
     */
    public function upload(string|Provider $provider, LocalFile $file): RemoteFile;

    public function delete(RemoteFile $file): void;
}
