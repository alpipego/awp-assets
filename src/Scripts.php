<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05.12.2016
 * Time: 17:33
 */
declare(strict_types = 1);

namespace Alpipego\AWP\Assets;

final class Scripts extends AbstractAssets
{
    public function __construct(array $scripts, $type = 'wp')
    {
        $this->collection = wp_scripts();
        $this->group      = 'script';
        parent::__construct($scripts, $type);
    }

    public function run()
    {
        parent::run();
        add_action("{$this->type}_enqueue_scripts", [$this, 'updateRegisteredGroup'], 190);
        add_filter('script_loader_tag', [$this, 'deferScripts'], 20, 2);
    }

    public function updateRegisteredGroup()
    {
        /** @var Script $script */
        foreach ($this->assets as &$script) {
            if (!$script->is_registered() || empty($group = $this->getGroup($script->handle))) {
                continue;
            }

            $this->remapFields($script);
            wp_deregister_script($script->handle);

            $registered = wp_scripts()->registered;

            array_walk($group, function ($handle) use ($script, $registered) {
                /** @var \_WP_Dependency $registeredScript */
                $registeredScript = $registered[$handle];

                $this->assets[] = (new Script($handle))
                    ->action('ignore')
                    ->src($registeredScript->src)
                    ->ver($registeredScript->ver ?: null)
                    ->deps($registeredScript->deps)
                    ->extra($registeredScript->extra ?? [])
                    ->prio($script->prio ?? '')
                    ->in_footer($script->in_footer);
                wp_deregister_script($handle);
            });
        }
    }

    protected function mergeUpdates(Asset $asset): Asset
    {
        /** @var Script $script */
        $script = parent::mergeUpdates($asset);
        if (is_null($script)) {
            return $asset;
        }
        $registered = wp_scripts()->registered[$script->handle];
        $script
            ->localize($script->localize ?? [])
            ->in_footer(!empty($registered->extra['group']));

        return $script;
    }

    public function register()
    {
        /** @var Script $script */
        foreach ($this->assets as $script) {
            $script = $this->mergeUpdates($script);
            $file   = '';
            if (is_null($script->src)) {
                $file = $this->getPath($script, 'js');
            }

            $script->src((string)($script->src ?? $this->getSrc($script, 'js')))
                   ->ver($script->ver ?: (string)(file_exists($file) ? filemtime($file) : ''));

            wp_register_script(
                $script->handle,
                $script->src,
                $script->deps,
                $script->ver,
                $script->in_footer
            );

            if (!empty($script->localize)) {
                $this->localize($script);
            }

            $this->mergeData($script);
        }
    }

    private function localize(Script $script)
    {
        array_walk($script->localize, function ($value, $key) use ($script) {
            wp_localize_script($script->handle, $key, $value);
        });
    }

    protected function extra(AssetInterface $script) : string
    {
        return wp_scripts()->registered[$script->handle]->extra['data'] ?? '';
    }

    public function deferScripts($tag, $handle)
    {
        /** @var Script $asset */
        foreach ($this->assets as $asset) {
            if ($asset->handle === $handle) {
                if ($asset->prio === 'defer') {
                    return str_replace(' src', ' defer="defer" src', $tag);
                }
                if ($asset->prio === 'async') {
                    return str_replace(' src', ' async="async" src', $tag);
                }
            }
        }

        return $tag;
    }
}
