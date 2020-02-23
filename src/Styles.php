<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05.12.2016
 * Time: 17:29
 */
declare(strict_types = 1);

namespace Alpipego\AWP\Assets;

use Alpipego\AWP\Assets\Exceptions\AssetNotFoundException;

final class Styles extends AbstractAssets implements AssetsInterface
{
    public function __construct(array $styles, $type = 'wp')
    {
        $this->collection = wp_styles();
        $this->group      = 'style';
        parent::__construct($styles, $type);
    }

    public function run()
    {
        parent::run();
        add_filter('style_loader_tag', [$this, 'lazyCss'], 10, 2);
    }

    protected function extra(AssetInterface $style): string
    {
        return '';
    }

    public function register()
    {
        /** @var Style $style */
        foreach ($this->assets as $style) {
            if ($style->is_registered()) {
                $this->remapFields($style);
                wp_deregister_style($style->handle);
            }

            $file = '';
            if (is_null($style->src)) {
                $file = $this->getPath($style, 'css');
            }

            $style
                ->src((string)($style->src ?? $this->getSrc($style, 'css')))
                ->ver($style->ver ?: (string)(file_exists($file) ? filemtime($file) : ''));

            wp_register_style(
                $style->handle,
                $style->src,
                $style->deps,
                $style->ver,
                $style->media
            );

            $this->mergeData($style);
        }
    }

    protected function mergeUpdates(Asset $asset): Asset
    {
        /** @var Style $style */
        $style = parent::mergeUpdates($asset);
        if (is_null($style)) {
            return $asset;
        }

        return $style;
    }

    public function lazyCss(string $tag, string $handle): string
    {
        try {
            $asset = $this->getAsset($handle);
        } catch (AssetNotFoundException $e) {
            return $tag;
        }

        if (empty($asset->prio) || $asset->prio !== 'defer') {
            return $tag;
        }

        $this->addLazyPolyfill();
        add_action('wp_head', function () use ($tag) {
            printf('<noscript>%s</noscript>', $tag);
        });

        if (!preg_match('/href=[\'"]([^\'"]+)[\'"]/i', $tag, $href)) {
            return $tag;
        }

        if (!preg_match('/media=[\'"]([^\'"]+)[\'"]/', $tag, $media)) {
            $media[1] = '';
        }

        return sprintf(
            '<link href="%s" as="style" media="%s" rel="preload" onload="this.onload=null;this.rel=\'stylesheet\'">',
            $href[1],
            $media[1]
        );
    }

    private function addLazyPolyfill()
    {
        static $present = false;
        if ($present) {
            return;
        }

        $present = true;

        add_action('wp_head', function () {
            printf('<script>%s</script>', file_get_contents(__DIR__ . '/../inc/csspreload.js'));
        });
    }
}
