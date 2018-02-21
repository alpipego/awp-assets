<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 21.02.2018
 * Time: 12:55
 */
declare(strict_types=1);

namespace WPHibou\Assets;

trait StyleTrait
{
    public function media(string $media = 'all'): AssetInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}
