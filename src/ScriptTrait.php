<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 21.02.2018
 * Time: 12:53
 */
declare(strict_types=1);

namespace WPHibou\Assets;

trait ScriptTrait
{
    public function in_footer(bool $in_footer = false): AssetInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}
