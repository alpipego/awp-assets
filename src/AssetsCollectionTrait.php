<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 28.09.2017
 * Time: 12:27
 */

namespace WPHibou\Assets;


trait AssetsCollectionTrait
{
    public function add(Asset $asset)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function remove(Asset $asset)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function update(Asset $asset)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function inline(Asset $asset)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}
