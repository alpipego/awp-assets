<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 28.09.2017
 * Time: 12:10
 */

namespace WPHibou\Assets;

interface AssetsCollectionInterface
{
    public function add(Asset $asset): ?AssetInterface;

    public function remove(Asset $asset): ?AssetInterface;

    public function inline(Asset $asset): ?AssetInterface;

    public function update(Asset $asset): ?AssetInterface;
}
