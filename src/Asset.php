<?php

namespace WPHibou\Assets;

/**
 * Class Asset
 * @package WPHibou\Assets
 *
 * @method $this min(bool $min = false)
 * @method $this src(string $src = '')
 * @method $this ver(string $ver = '')
 * @method $this deps(array $deps = [])
 * @method $this extra(array $extra = [])
 * @method $this action(string $action = '')
 * @method $this prio(string $prio = '')
 * @method $this localize(array $localize = [])
 * @method $this data(array $data = [])
 * @method $this footer(bool $footer = false)
 */
class Asset
{
    public $handle;
    public $condition = true;
    public $src = '';
    public $ver = null;
    public $deps = [];
    public $extra = [];
    public $action = '';
    public $prio = '';
    public $localize = [];
    public $min = false;
    public $data = [];
    public $footer = false;

    public function __construct($handle)
    {
        $this->handle = $handle;
    }

    public function __call($name, $args)
    {
        return $this->__set($name, $args[0]);
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            if (is_array($this->$name)) {
                if ( ! is_array($value)) {
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

    public function condition(callable $cond)
    {
        if ( ! did_action('wp')) {
            add_action('wp', function () use ($cond) {
                $this->condition = call_user_func($cond);
            });
        } else {
            $this->condition = call_user_func($cond);
        }


        return $this;
    }
}
