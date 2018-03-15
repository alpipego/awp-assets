<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05.12.2016
 * Time: 17:29
 */
declare(strict_types=1);

namespace WPHibou\Assets;

final class Styles extends AbstractAssets
{
    public function __construct(array $styles, $type = 'wp')
    {
        $this->collection = wp_styles();
        $this->group  = 'style';
        parent::__construct($styles, $type);
    }

    public function run()
    {
        parent::run();
        add_filter('style_loader_tag', [$this, 'lazyCss'], 10, 2);
    }

    public function register()
    {
        /** @var Asset $style */
        foreach ($this->assets as $style) {
            if ($style->is_registered()) {
                $this->remapFields($style);
                wp_deregister_style($style->handle);
            }

            wp_register_style(
                $style->handle,
                $style->src ?? $this->getSrc($style, 'css'),
                $style->deps,
                $style->ver ?? file_exists($file = $this->getPath($style, 'css')) ? filemtime($file) : false,
                $this->media ?? 'screen'
            );

            $this->mergeData($style);
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
