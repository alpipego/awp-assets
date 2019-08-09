<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05.12.2016
 * Time: 13:38
 */
declare(strict_types = 1);

namespace Alpipego\AWP\Assets;

final class Script extends Asset implements ScriptInterface, AssetsResolverInterface
{
    public $in_footer = true;
    public $localize = [];

    public function in_footer(bool $in_footer = true) : ScriptInterface
    {
        $this->args = $in_footer;

        return $this->__call(__FUNCTION__, [$in_footer]);
    }

    public function localize(array $localize = []) : ScriptInterface
    {
        return $this->__call(__FUNCTION__, [$localize]);
    }
}
