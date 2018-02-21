<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 28.09.2017
 * Time: 12:27
 */
declare(strict_types=1);

namespace WPHibou\Assets;

trait AssetsCollectionTrait
{
    public function add(Asset $asset): ?Asset
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function remove(Asset $asset): ?Asset
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function update(Asset $asset): ?Asset
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function inline(Asset $asset): ?Asset
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}
