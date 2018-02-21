<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05.12.2016
 * Time: 13:49
 */

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
        add_action('wp_enqueue_scripts', [$this, 'register'], 11);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets'], 12);
    }

    abstract public function register();

    public function enqueueAssets()
    {
        /** @var Asset $asset */
        foreach ($this->assets as $asset) {
            $asset = $this->action($asset);
            $this->{$asset->action}($asset);
        }
    }

    protected function action(Asset $asset)
    {
        if (! isset($asset->action)) {
            return $asset;
        }

        $merged = array_merge((array)$this->collection->registered[$asset->handle], array_filter((array)$asset));

        if ($asset->action === 'add') {
            if (in_array($asset->handle, $this->collection->queue, true)) {
                // asset is already queued (possibly changed condition)
                foreach ($merged as $field => $value) {
                    $asset->$field = $value;
                }
                $asset->action = 'requeue';
            } else {
                $asset->action = 'enqueue';
            }
        } elseif ($asset->action === 'remove') {
            // asset is queued, should be removed (still looks at condition)
            $asset->action = 'dequeue';
        } elseif ($asset->action === 'inline' || $asset->action === 'update') {
            foreach ($merged as $field => $value) {
                $asset->$field = $value;
            }
        }

        return $asset;
    }

    protected function getSrc(Asset $asset, string $fragment): string
    {
        $assetDir = apply_filters('wp-hibou/assets/dir', get_stylesheet_directory_uri());
        $handle   = $asset->min ? $asset->handle . '.min' : $asset->handle;

        return sprintf('%1$s/%2$s/%3$s.%2$s', untrailingslashit($assetDir), $fragment, $handle);
    }

    protected function getPath(Asset $asset, string $fragment): string
    {
        $assetPath = apply_filters('wp-hibou/assets/path', get_stylesheet_directory());
        $handle    = $asset->min ? $asset->handle . '.min' : $asset->handle;

        return sprintf('%1$s/%2$s/%3$s.%2$s', untrailingslashit($assetPath), $fragment, $handle);
    }

    private function requeue(Asset $asset)
    {
        if ($this->isQueued($asset)) {
            $func = "wp_dequeue_{$this->group}";
            $func($asset->handle);
            $this->enqueue($asset);
        }
    }

    private function isQueued(Asset $asset): bool
    {
        return wp_script_is($asset->handle);
    }

    private function enqueue(Asset $asset)
    {
        if ($asset->condition) {
            if ($this->group === 'style' && $asset->in_footer) {
                add_action('wp_footer', function () use ($asset) {
                    $func = "wp_enqueue_{$this->group}";
                    $func($asset->handle);
                });
            } else {
                $func = "wp_enqueue_{$this->group}";
                $func($asset->handle);
            }
        }
    }

    private function update(Asset $asset)
    {
        foreach ($asset->data as $key => $value) {
            $this->collection->add_data($asset->handle, $key, $value);
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
                            $func = "wp_add_inline_{$this->group}";
                            $func($dependency, $contents);
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

    private function dequeue(Asset $asset)
    {
        if ($asset->condition) {
            $func = "wp_dequeue_{$this->group}";
            $func($asset->handle);
            // check if this is part of an aliased dependency group
            $this->dequeueAlias($asset);
        }
    }

    private function dequeueAlias(Asset $asset)
    {
        $aliases = [];
        foreach (array_column($this->collection->registered, 'deps', 'handle') as $alias => $deps) {
            if (in_array($asset->handle, $deps, true) && empty($this->collection->registered[$alias]->src)) {
                $aliases[] = $alias;
            }
        }

        foreach ($aliases as $alias) {
            $this->collection->registered[$alias]->deps = array_diff(
                $this->collection->registered[$alias]->deps,
                [$asset->handle]
            );
        }
    }
}
