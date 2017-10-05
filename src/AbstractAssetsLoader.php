<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05.12.2016
 * Time: 12:52
 */

namespace WPHibou\Assets;

abstract class AbstractAssetsLoader
{
    private $handle;
    private $deps;
    private $ver;

    public function add(string $handle): string
    {
        $this->handle = $handle;

        return $this;
    }

    /**
     * Add dependecies for this assets
     *
     * @param string|array $deps
     *
     * @return $this
     */
    public function deps($deps): array
    {
        $this->deps = is_array($deps) ? $deps : [$deps];

        return $this;
    }

    abstract function callback();
}
