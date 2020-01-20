<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 21.02.2018
 * Time: 14:32
 */
declare(strict_types = 1);

namespace Alpipego\AWP\Assets;

trait AssetsResolverTrait
{
    public function state()
    {
        return [
            'registered' => $this->is_registered(),
            'enqueued'   => $this->is_enqueued(),
            'to_do'      => $this->is_to_do(),
            'done'       => $this->is_done(),
        ];
    }

    public function is_registered(string $handle = null, string $type = 'script'): bool
    {
        return $this->is(__FUNCTION__, $handle, $type);
    }

    private function is(string $name, string $handle = null, string $type = ''): bool
    {
        if (!in_array($type, ['style', 'script'], true)) {
            return false;
        }

        $state  = strtolower(str_replace('is_', '', $name));
        $handle = $handle ?? $this instanceof Asset ? $this->handle : '';
        if (empty($handle)) {
            return false;
        }

        if (empty($type) && $this instanceof Asset) {
            try {
                $type = strtolower((new \ReflectionClass($this))->getShortName());
            } catch (\ReflectionException $e) {
                return false;
            }
        }

        $func = "wp_{$type}_is";

        return $func($handle, $state);
    }

    public function is_enqueued(string $handle = null, string $type = 'script'): bool
    {
        return $this->is(__FUNCTION__, $handle, $type);
    }

    public function is_to_do(string $handle = null, string $type = 'script'): bool
    {
        return $this->is(__FUNCTION__, $handle, $type);
    }

    public function is_done(string $handle = null, string $type = 'script'): bool
    {
        return $this->is(__FUNCTION__, $handle, $type);
    }
}
