<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05.12.2016
 * Time: 17:29
 */
declare(strict_types=1);

namespace WPHibou\Assets;

class Styles extends AbstractAssets
{
    public function __construct(array $assets)
    {
        $this->assets     = $assets;
        $this->collection = wp_styles();
        $this->group      = 'style';
    }

    public function run()
    {
        parent::run();
        add_filter('style_loader_tag', [$this, 'lazyCss'], 10, 2);
    }

    public function register()
    {
        foreach ($this->assets as $style) {
            if (! array_key_exists($style->handle, $this->collection->registered) && $style->action !== 'remove') {
                wp_register_style(
                    $style->handle,
                    $style->src ?: $this->getSrc($style, 'css'),
                    $style->deps ?? [],
                    $style->ver ?? filemtime($this->getPath($style, 'css')),
                    $this->media ?? 'screen'
                );
                foreach (array_merge($style->data, $style->extra) as $key => $data) {
                    $this->collection->add_data($style->handle, $key, $data);
                }
            }
        }
    }

    public function lazyCss($tag, $handle)
    {
        /** @var Asset $asset */
        foreach ($this->assets as $asset) {
            if ($asset->handle === $handle && ! empty($asset->prio) && $asset->prio === 'defer') {
                add_action('wp_head', function () use ($tag) {
                    printf('<noscript>%s</noscript>', $tag);
                });

                return preg_replace(
                    '%href=(.[^\'\"].)%',
                    'href data-href=$1',
                    preg_replace('%media=([^\s/]+)%', 'media="defer" data-media=$1', $tag)
                );
            }
        }

        return $tag;
    }
}
