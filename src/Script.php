<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05.12.2016
 * Time: 13:38
 */
declare(strict_types = 1);

namespace Alpipego\AWP\Assets;

/**
 * Class Script
 * @package Alpipego\AWP\Assets
 *
 * @method ScriptInterface min(bool $min = false)
 * @method ScriptInterface src(string $src = null)
 * @method ScriptInterface ver(string $ver = null)
 * @method ScriptInterface deps(array $deps = [])
 * @method ScriptInterface action(string $action = '')
 * @method ScriptInterface extra(array $extra = [])
 * @method ScriptInterface prio(string $prio = '')
 * @method ScriptInterface data(array $data = [])
 * @method ScriptInterface position(string $position = 'after')
 * @method ScriptInterface condition(callable $cond)
 */
class Script extends Asset implements ScriptInterface, AssetsResolverInterface
{
    public $in_footer = true;
    public $localize = [];

    public function in_footer(bool $in_footer = true): ScriptInterface
    {
        $this->args = $in_footer;

        return $this->__call(__FUNCTION__, [$in_footer]);
    }

    public function localize(array $localize = []): ScriptInterface
    {
        return $this->__call(__FUNCTION__, [$localize]);
    }
}
