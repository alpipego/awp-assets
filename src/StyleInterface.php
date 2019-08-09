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
 * @method  min(bool $min = false): self
 * @method src(string $src = null): self
 * @method ver(string $ver = null): self
 * @method deps(array $deps = []): self
 * @method extra(array $extra = []): self
 * @method action(string $action = ''): self
 * @method prio(string $prio = ''): self
 * @method data(array $data = []): self
 * @method condition(callable $cond): self
 * @method position(string $position = 'after'): self
 */
interface StyleInterface extends AssetInterface
{
    public function media(string $media = 'screen'): StyleInterface;
}
