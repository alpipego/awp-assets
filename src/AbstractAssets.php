<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05.12.2016
 * Time: 13:49
 */
declare(strict_types=1);

namespace Alpipego\AWP\Assets;

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
    protected $type;

    public function __construct(array $assets, string $type = 'wp')
    {
        if (! in_array(trim(strtolower($type)), ['wp', 'login', 'admin'], true)) {
            $type = 'wp';
        }
        $this->assets = $assets;
        $this->type = $type;
    }

    public function run()
    {
        add_action("{$this->type}_enqueue_scripts", [$this, 'register'], 200);
        add_action("{$this->type}_enqueue_scripts", [$this, 'enqueueAssets'], 300);
        add_action('wp_print_footer_scripts', [$this, 'forceDequeue'], 1);
    }

    public function forceDequeue()
    {
        array_walk($this->assets, function (Asset $asset) {
            if (in_array($asset->action, ['dequeue', 'requeue'], true)) {
                $this->{$asset->action}($asset);
            }
        });
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

        if (in_array($asset->action, ['add', 'update'], true)) {
            if ($asset->is_enqueued()) {
                // asset is already queued (possibly changed condition)
                $this->remapFields($asset);
                $asset->action = 'requeue';
            } else {
                $asset->action = 'enqueue';
            }
        } elseif ($asset->action === 'remove') {
            // asset is queued, should be removed (still looks at condition)
            $asset->action = 'dequeue';
        } elseif ($asset->action === 'inline') {
            $this->remapFields($asset);
        }

        return $asset;
    }

    protected function remapFields(Asset &$asset)
    {
        $fields = array_merge(
            (array)$this->collection->registered[$asset->handle],
            array_filter((array)$asset, function ($value) {
                if (! is_null($value)) {
                    if (is_array($value)) {
                        return ! empty($value);
                    }

                    return true;
                }
            })
        );

        array_walk($fields, function ($value, $field) use (&$asset) {
            $asset->{$field} = $value;
        });
    }

    protected function mergeData(Asset $asset)
    {
        foreach (array_merge($asset->data, $asset->extra) as $key => $data) {
            $this->collection->add_data($asset->handle, $key, $data);
        }
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
            call_user_func("wp_dequeue_{$this->group}", $asset->handle);
        }

        $this->enqueue($asset);
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

    private function ignore(Asset $asset)
    {
        // do nothing
    }

    private function inline(Asset $asset)
    {
        if (! $asset->condition) {
            return false;
        }

        $path = preg_replace('%^(?:https?:)?//[^/]+(/.+)$%i', '$1', $asset->src);
        $file = array_filter([ABSPATH . $path, ABSPATH . '..' . $path], 'file_exists');
        if (empty($file)) {
            return false;
        }

        $file = array_shift($file);
        if (filesize($file) <= (int)apply_filters('wp-hibou/assets/inline/filesize', 2000)) {
            $this->dequeue($asset);

            $contents = file_get_contents($file);
            // replace single line comments
            $contents = preg_replace('%(?:^\s*[/]{2}.+$)%m', '', $contents);
            // replace multi line comments
            $contents = preg_replace('%(?:\s*/\*+.+/)%s', '', $contents);

            $dependencies = [];
            foreach ($asset->deps as $dep) {
                if (! empty($group = $this->getGroup($dep))) {
                    foreach ($group as $item) {
                        $dependencies[] = $item;
                    }
                    continue;
                }

                $dependencies[] = $dep;
            }
            array_unique($dependencies);

            if (count($dependencies) === 1) {
                call_user_func("wp_add_inline_{$this->group}", end($dependencies), $contents);

                return true;
            }

            $inlined = false;
            foreach ($dependencies as $dependency) {
                add_action('wp_head', function () use ($contents, $dependency, $inlined) {
                    if (wp_script_is($dependency, 'done') && ! $inlined) {
                        printf('<%1$s>%2$s</%1$s>', $this->group, $contents);
                        $inlined = true;
                    }
                    if (! $inlined) {
                        add_action('wp_footer', function () use ($contents, $dependency, $inlined) {
                            if (wp_script_is($dependency, 'done') && ! $inlined) {
                                printf('<%1$s>%2$s</%1$s>', $this->group, $contents);
                                $inlined = true;
                            }
                        }, 600);
                    }
                }, 600);

                return true;
            }


            $action = isset($asset->in_footer) && $asset->in_footer ? 'wp_footer' : 'wp_head';
            add_action($action, function () use ($contents) {
                printf('<%1$s>%2$s</%1$s>', $this->group, $contents);
            });

            return true;
        }

        $this->enqueue($asset);

        return true;
    }

    private function dequeue(Asset $asset)
    {
        if ($asset->condition) {
            call_user_func("wp_dequeue_{$this->group}", $asset->handle);
            // check if this is part of an aliased dependency group
            $this->dequeueGroup($asset->handle);
            $this->dequeueGroupMember($asset->handle);
        }
    }

    private function dequeueGroup(string $handle)
    {
        $group = $this->getGroup($handle);
        array_walk($group, function ($value) use ($handle) {
            if (empty($this->collection->registered[$value]->src)) {
                $this->dequeueGroup($value);
            }
            $this->collection->registered[$handle]->deps = [];
            call_user_func("wp_dequeue_{$this->group}", $value);
        });
    }

    protected function getGroup(string $handle): array
    {
        $alias = new \_WP_Dependency();
        if (
            array_key_exists($handle, $this->collection->registered)
            && empty($this->collection->registered[$handle]->src)
        ) {
            $alias = $this->collection->registered[$handle];
        }

        return $alias->deps;
    }

    private function dequeueGroupMember(string $handle)
    {
        $groupMembers = $this->getGroupMember($handle);
        array_walk($groupMembers, function (string $alias) use ($handle) {
            $this->collection->registered[$alias]->deps = array_diff(
                $this->collection->registered[$alias]->deps,
                [$handle]
            );
        });
    }

    protected function getGroupMember(string $handle): array
    {
        return array_keys(
            array_filter(
                array_column($this->collection->registered, 'deps', 'handle'),
                function ($deps, $alias) use ($handle) {
                    if (in_array($handle, $deps, true)) {
                        return $alias;
                    }
                },
                ARRAY_FILTER_USE_BOTH
            )
        );
    }
}
