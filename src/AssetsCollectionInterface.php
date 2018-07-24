<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 28.09.2017
 * Time: 12:10
 */

namespace Alpipego\AWP\Assets;

interface AssetsCollectionInterface
{
    public function add(AssetInterface $asset): ?AssetInterface;

    public function remove(AssetInterface $asset): ?AssetInterface;

    public function inline(AssetInterface $asset): ?AssetInterface;

    public function update(AssetInterface $asset): ?AssetInterface;
}
