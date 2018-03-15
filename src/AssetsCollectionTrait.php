<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 28.09.2017
 * Time: 12:27
 */
declare(strict_types=1);

namespace Alpipego\AWP\Assets;

trait AssetsCollectionTrait
{
    public function add(Asset $asset): ?AssetInterface
    {
        return $this->__call(__FUNCTION__, $asset);
    }

    public function remove(Asset $asset): ?AssetInterface
    {
        return $this->__call(__FUNCTION__, $asset);
    }

    public function update(Asset $asset): ?AssetInterface
    {
        return $this->__call(__FUNCTION__, $asset);
    }

    public function inline(Asset $asset): ?AssetInterface
    {
        return $this->__call(__FUNCTION__, $asset);
    }
}
