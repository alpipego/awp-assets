<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 21.02.2018
 * Time: 12:50
 */
declare(strict_types=1);

namespace Alpipego\AWP\Assets;

/**
 * Interface StyleInterface
 * @package Alpipego\AWP\Assets
 *
 * @method StyleInterface min(bool $min = false)
 * @method StyleInterface src(string $src = null)
 * @method StyleInterface ver(string $ver = null)
 * @method StyleInterface deps(array $deps = [])
 * @method StyleInterface extra(array $extra = [])
 * @method StyleInterface action(string $action = '')
 * @method StyleInterface prio(string $prio = '')
 * @method StyleInterface data(array $data = [])
 * @method StyleInterface condition(callable $cond)
 * @method StyleInterface position(string $position = 'after')
 */
interface StyleInterface extends AssetInterface
{
    public function media(string $media = 'screen'): StyleInterface;
}
