<?php

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
 * @method ScriptInterface extra(array $extra = [])
 * @method ScriptInterface action(string $action = '')
 * @method ScriptInterface prio(string $prio = '')
 * @method ScriptInterface data(array $data = [])
 * @method ScriptInterface position(string $position = 'after')
 * @method ScriptInterface condition(callable $cond)
 * @method ScriptInterface in_footer(bool $in_footer = true)
 * @method ScriptInterface localize(array $localize = [])
 */
class ScriptUpdate extends Script implements ScriptInterface, AssetsResolverInterface
{
    public const UPDATER = true;

    public function __construct(string $handle)
    {
        $this->resetDefaults($this);
        parent::__construct($handle);
    }
}
