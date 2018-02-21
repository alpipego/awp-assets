<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05.12.2016
 * Time: 13:49
 */
declare(strict_types=1);

namespace WPHibou\Assets;

abstract class AbstractAssets
{
    protected $assets = [];
    /**
     * @var \WP_Styles | \WP_Scripts $collection
     */
    protected $collection;
    /**
     * @var string $group either 'style' or 'script'
     */
    protected $group;

    public function run()
    {
        add_action('wp_enqueue_scripts', [$this, 'register'], 20);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets'], 30);
    }

    abstract public function register();

    public function enqueueAssets()
    {
        array_walk($this->assets, function (Asset $asset) {
            $asset = $this->action($asset);
            $this->{$asset->action}($asset);
        });
    }

    protected function action(Asset $asset)
    {
        if (! isset($asset->action)) {
            return $asset;
        }

        $merged = array_merge((array)$this->collection->registered[$asset->handle], array_filter((array)$asset));

        if ($asset->action === 'add' || $asset->action === 'update') {
            if (in_array($asset->handle, $this->collection->queue, true)) {
                // asset is already queued (possibly changed condition)
                $this->remapFields($merged, $asset);
                $asset->action = 'requeue';
            } else {
                $asset->action = 'enqueue';
            }
        } elseif ($asset->action === 'remove') {
            // asset is queued, should be removed (still looks at condition)
            $asset->action = 'dequeue';
        } elseif ($asset->action === 'inline') {
            $this->remapFields($merged, $asset);
        }

        return $asset;
    }

    private function remapFields(array $fields, Asset &$asset)
    {
        array_walk($fields, function ($value, $field) use (&$asset) {
            $asset->$field = $value;
        });
    }

    protected function getSrc(Asset $asset, string $fragment): string
    {
        $assetDir = (string)apply_filters('wp-hibou/assets/dir', get_stylesheet_directory_uri());
        $handle   = $asset->min ? $asset->handle . '.min' : $asset->handle;

        return sprintf('%1$s/%2$s/%3$s.%2$s', untrailingslashit($assetDir), $fragment, $handle);
    }

    protected function getPath(Asset $asset, string $fragment): string
    {
        $assetPath = (string)apply_filters('wp-hibou/assets/path', get_stylesheet_directory());
        $handle    = $asset->min ? $asset->handle . '.min' : $asset->handle;

        return sprintf('%1$s/%2$s/%3$s.%2$s', untrailingslashit($assetPath), $fragment, $handle);
    }

    private function requeue(Asset $asset)
    {
        if ($asset->is_enqueued()) {
            $asset->condition = ! $asset->condition;
            $this->dequeue($asset);
            $asset->condition = ! $asset->condition;
        }

        $this->enqueue($asset);
    }

    private function dequeue(Asset $asset)
    {
        if ($asset->condition) {
            call_user_func("wp_dequeue_{$this->group}", $asset->handle);
            // check if this is part of an aliased dependency group
            $this->dequeueAliased($asset->handle);
            $this->dequeueAlias($asset->handle);
        }
    }

    private function dequeueAliased(string $handle)
    {
        $aliases = [];
        foreach (array_column($this->collection->registered, 'deps', 'handle') as $alias => $deps) {
            if (in_array($handle, $deps, true) && empty($this->collection->registered[$alias]->src)) {
                $aliases[] = $alias;
            }
        }

        foreach ($aliases as $alias) {
            $this->collection->registered[$alias]->deps = array_diff(
                $this->collection->registered[$alias]->deps,
                [$handle]
            );
        }
    }

    private function dequeueAlias(string $handle)
    {
        $alias = [];
        if (
            array_key_exists($handle, $this->collection->registered)
            && empty($this->collection->registered[$handle]->src)
        ) {
            $alias = $this->collection->registered[$handle];
        }

        if (empty($alias)) {
            return;
        }
        array_walk($alias->deps, function ($value) use ($handle) {
            if (empty($this->collection->registered[$value]->src)) {
                $this->dequeueAlias($value);
            }
            $this->collection->registered[$handle]->deps = [];
            call_user_func("wp_dequeue_{$this->group}", $value);
        });
    }

    private function enqueue(Asset $asset)
    {
        if ($asset->condition) {
            array_walk($asset->data, function ($value, $key) use ($asset) {
                $this->collection->add_data($asset->handle, $key, $value);
            });
            call_user_func("wp_enqueue_{$this->group}", $asset->handle);
        }
    }

    private function inline(Asset $asset)
    {
        if ($asset->condition) {
            $path = preg_replace('%^(?:https?:)?//[^/]+(/.+)$%i', '$1', $asset->src);
            $file = array_filter([ABSPATH . $path, ABSPATH . '..' . $path], 'file_exists');
            if (! empty($file)) {
                $file = array_shift($file);
                if (filesize($file) < apply_filters('wp-hibou/assets/inline/filesize', 2000)) {
                    $contents = file_get_contents($file);
                    // replace single line comments
                    $contents = preg_replace('%(?:^\s*[/]{2}.+$)%m', '', $contents);
                    // replace multi line comments
                    $contents = preg_replace('%(?:\s*/\*+.+/)%s', '', $contents);
                    $this->dequeue($asset);

                    $dependency = end($asset->deps);
                    $action     = isset($asset->footer) && $asset->footer ? 'wp_footer' : 'wp_head';
                    if ($dependency) {
                        if ($dependency === 'jquery') {
                            add_action($action, function () use ($contents) {
                                if (wp_script_is('jquery', 'done')) {
                                    printf('<%1$s>%2$s</%1$s>', $this->group, $contents);
                                }
                            });
                        } else {
                            call_user_func("wp_add_inline_{$this->group}", $dependency, $contents);
                        }

                        return true;
                    } else {
                        add_action($action, function () use ($contents) {
                            printf('<%1$s>%2$s</%1$s>', $this->group, $contents);
                        });

                        return true;
                    }
                }
            }
        }

        $this->enqueue($asset);
    }
}
