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
 * class Style
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
class Style extends Asset implements StyleInterface, AssetsResolverInterface
{
    const TYPE = 'style';
    public $media = 'screen';

    public function media(string $media = 'screen'): StyleInterface
    {
        $this->args = $media;

        return $this->__call(__FUNCTION__, [$media]);
    }
}
