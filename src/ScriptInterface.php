<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 21.02.2018
 * Time: 12:50
 */
declare(strict_types = 1);

namespace Alpipego\AWP\Assets;

/**
 * Interface ScriptInterface
 * @package Alpipego\AWP\Assets
 *
 * @method ScriptInterface min(bool $min = false)
 * @method ScriptInterface src(string $src = null)
 * @method ScriptInterface ver(string $ver = null)
 * @method ScriptInterface deps(array $deps = [])
 * @method ScriptInterface extra(array $extra = [])
 * @method ScriptInterface action(string $action = '')
 * @method ScriptInterface prio(string $prio = '')
 * @method ScriptInterface data(array $data = [])
 * @method ScriptInterface position(string $position = 'after')
 * @method ScriptInterface condition(callable $cond)
 */
interface ScriptInterface extends AssetInterface
{
    public function in_footer(bool $in_footer = false) : self;

    public function localize(array $localize = []) : self;
}
