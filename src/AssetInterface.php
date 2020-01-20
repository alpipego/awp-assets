<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 21.02.2018
 * Time: 12:34
 */
declare(strict_types = 1);

namespace Alpipego\AWP\Assets;

interface AssetInterface
{
    public function min(bool $min = false): self;

    public function src(string $src = null): self;

    public function ver(string $ver = null): self;

    public function deps(array $deps = []): self;

    public function extra(array $extra = []): self;

    public function action(string $action = ''): self;

    public function prio(string $prio = ''): self;

    public function data(array $data = []): self;

    public function condition(callable $cond): self;

    public function position(string $position = 'after'): self;
}
