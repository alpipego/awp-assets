<?php
declare(strict_types=1);

namespace Alpipego\AWP\Assets;

/**
 * Class Asset
 * @package Alpipego\AWP\Assets
 *
 * @method $this min(bool $min = false): AssetInterface
 * @method $this src(string $src = null): AssetInterface
 * @method $this ver(string $ver = null): AssetInterface
 * @method $this deps(array $deps = []): AssetInterface
 * @method $this extra(array $extra = []): AssetInterface
 * @method $this action(string $action = ''): AssetInterface
 * @method $this prio(string $prio = ''): AssetInterface
 * @method $this data(array $data = []): AssetInterface
 * @method $this position(string $position = 'after'): AssetInterface
 */
class Asset implements AssetInterface, AssetsResolverInterface
{
    use AssetTrait;
    use AssetsResolverTrait;

    public $handle;
    public $condition = true;
    public $src = null;
    public $ver = null;
    public $deps = [];
    public $extra = [];
    public $action = '';
    public $prio = '';
    public $min = false;
    public $data = [];
    public $args = null;
    public $pos = 'after';

    public function __construct($handle)
    {
        $this->handle = $handle;
    }

    public function __call($name, $args)
    {
        if (! isset($args[0])) {
            return $this;
        }

        return $this->__set($name, $args[0]);
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            if (is_array($this->$name)) {
                if (! is_array($value)) {
                    $this->$name[] = $value;
                } else {
                    $this->$name = array_merge($this->$name, $value);
                }
            } else {
                $this->$name = $value;
            }
        }

        return $this;
    }

    public function condition(callable $cond) : AssetInterface
    {
        if (! did_action('wp')) {
            add_action('wp', function () use ($cond) {
                $this->condition = call_user_func($cond, $this);
            });
        } else {
            $this->condition = call_user_func($cond, $this);
        }


        return $this;
    }
}
