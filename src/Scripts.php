<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05.12.2016
 * Time: 17:33
 */
declare(strict_types=1);

namespace WPHibou\Assets;

final class Scripts extends AbstractAssets
{
    public function __construct(array $scripts)
    {
        $this->assets     = $scripts;
        $this->collection = wp_scripts();
        $this->group      = 'script';
    }

    public function run()
    {
        parent::run();
        add_action('wp_enqueue_scripts', [$this, 'updateRegisteredGroup'], 199);
        add_filter('script_loader_tag', [$this, 'deferScripts'], 20, 2);
    }

    public function updateRegisteredGroup()
    {
        /** @var Script $script */
        foreach ($this->assets as &$script) {
            if (! $script->is_registered()) {
                continue;
            }

            $this->remapFields($script);

            if (! empty($group = $this->getGroup($script->handle))) {
                $script->condition('__return_false');
                $script->action('remove');
                $registered = wp_scripts()->registered;

                array_walk($group, function ($handle) use ($script, $registered) {
                    /** @var \_WP_Dependency $registeredScript */
                    $registeredScript = $registered[$handle];
                    $dep              = (new Script($handle))
                        ->action('ignore')
                        ->src($registeredScript->src)
                        ->ver($registeredScript->ver)
                        ->deps($registeredScript->deps)
                        ->extra($registeredScript->extra ?? [])
                        ->prio($script->prio)
                        ->condition('__return_false')
                        ->in_footer($script->in_footer);
                    wp_deregister_script($handle);
                    $this->assets[] = $dep;
                });
            }
            wp_deregister_script($script->handle);
        }
    }

    public function register()
    {
        /** @var Script $script */
        foreach ($this->assets as $script) {
            wp_register_script(
                $script->handle,
                $script->src ?? $this->getSrc($script, 'js'),
                $script->deps,
                $script->ver ?? (file_exists($path = $this->getPath($script, 'js')) ? filemtime($path) : ''),
                $script->in_footer ?? true
            );

            if (! empty($script->localize)) {
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
