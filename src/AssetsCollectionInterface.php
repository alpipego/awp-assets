<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 28.09.2017
 * Time: 12:10
 */

namespace WPHibou\Assets;

interface AssetsCollectionInterface
{
    public function add(Asset $asset);

    public function remove(Asset $asset);

    public function inline(Asset $asset);

    public function update(Asset $asset);
}
