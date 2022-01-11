<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 28.09.2017
 * Time: 12:27
 */
declare(strict_types = 1);

namespace Alpipego\AWP\Assets;

trait AssetsCollectionTrait
{
    public function add(AssetInterface $asset): ?AssetInterface
    {
        return $this->__call(__FUNCTION__, [$asset]);
    }

    public function remove(AssetInterface $asset): ?AssetInterface
    {
        return $this->__call(__FUNCTION__, [$asset]);
    }

    public function update(AssetInterface $asset): ?AssetInterface
    {
        return $this->__call(__FUNCTION__, [$asset]);
    }

    public function inline(AssetInterface $asset): ?AssetInterface
    {
        return $this->__call(__FUNCTION__, [$asset]);
    }
}
