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
 * @method $this min(bool $min = false): self
 * @method $this src(string $src = null): self
 * @method $this ver(string $ver = null): self
 * @method $this deps(array $deps = []): self
 * @method $this extra(array $extra = []): self
 * @method $this action(string $action = ''): self
 * @method $this prio(string $prio = ''): self
 * @method $this data(array $data = []): self
 * @method $this position(string $position = 'after'): self
 * @method $this condition(callable $cond) : self
 */
interface ScriptInterface extends AssetInterface
{
    public function in_footer(bool $in_footer = false) : self;

    public function localize(array $localize = []) : self;
}
