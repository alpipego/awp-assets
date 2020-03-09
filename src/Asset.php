<?php
declare(strict_types = 1);

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
abstract class Asset implements AssetInterface, AssetsResolverInterface
{
    use AssetTrait;

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
    protected $state = '';

    public function __construct(string $handle)
    {
        $this->handle = $handle;
    }

    public function __call($name, $args)
    {
        if (!isset($args[0])) {
            return $this;
        }

        return $this->__set($name, $args[0]);
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            if (is_array($this->$name)) {
                if (!is_array($value)) {
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

    public function condition(callable $cond): AssetInterface
    {
        if (!did_action('wp')) {
            add_action('wp', function () use ($cond) {
                $this->condition = call_user_func($cond, $this);
            });
        } else {
            $this->condition = call_user_func($cond, $this);
        }


        return $this;
    }

    public function state(): array
    {
        return array_keys(array_filter([
            'registered' => $this->is_registered(),
            'enqueued'   => $this->is_enqueued(),
            'inlined'    => $this->is_inlined(),
            'to_do'      => $this->is_to_do(),
            'done'       => $this->is_done(),
        ]));
    }

    public function is_inlined(): bool
    {
        return $this->action === 'inline';
    }

    public function is_registered(string $handle = null, string $type = 'script'): bool
    {
        return $this->is(__FUNCTION__);
    }

    public function is_enqueued(string $handle = null, string $type = 'script'): bool
    {
        return $this->is(__FUNCTION__);
    }

    public function is_to_do(): bool
    {
        return $this->state === 'to_do' || $this->is(__FUNCTION__);
    }

    public function is_done(): bool
    {
        return $this->state === 'done' || $this->is(__FUNCTION__);
    }

    private function is(string $state): bool
    {
        $state = strtolower(str_replace('is_', '', $state));
        $type  = $this->getType();
        $func  = "wp_{$type}_is";

        return $func($this->handle, $state);
    }

    public function done()
    {
        $this->state = 'done';
    }

    public function to_do()
    {
        $this->state = 'to_do';
    }

    protected function resetDefaults(Asset &$asset)
    {
        foreach (get_object_vars($asset) as $property => $value) {
            $asset->{$property} = null;
        }
    }

    protected function getType(): string
    {
        return static::TYPE;
    }
}
