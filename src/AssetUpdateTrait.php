<?php

declare(strict_types = 1);

namespace Alpipego\AWP\Assets;

trait AssetUpdateTrait
{
    protected function resetDefaults(Asset &$asset)
    {
        foreach (get_object_vars($asset) as $property => $value) {
            $asset->{$property} = null;
        }
    }
}
