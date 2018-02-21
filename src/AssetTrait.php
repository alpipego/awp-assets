<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 21.02.2018
 * Time: 12:39
 */
declare(strict_types=1);

namespace WPHibou\Assets;

trait AssetTrait
{
    public function min(bool $min = false): AssetInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function src(string $src = null): AssetInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function ver(string $ver = ''): AssetInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function deps(array $deps = []): AssetInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function extra(array $extra = []): AssetInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function action(string $action = ''): AssetInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function prio(string $prio = ''): AssetInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function localize(array $localize = []): AssetInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function data(array $data = []): AssetInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function in_footer(bool $in_footer = false): AssetInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}
