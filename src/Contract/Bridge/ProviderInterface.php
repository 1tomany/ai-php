<?php

namespace OneToMany\AI\Contract\Bridge;

use OneToMany\AI\Vendor;

interface ProviderInterface
{
    public function vendor(): Vendor;
}
