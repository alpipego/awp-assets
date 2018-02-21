<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 06.12.2016
 * Time: 09:41
 */
declare(strict_types=1);

namespace WPHibou\Assets;

/**
 * Class AssetsCollection
 * @package WPHibou\Assets
 *
 * @method $this add(Asset $asset): ?AssetInterface
 * @method $this remove(Asset $asset): ?AssetInterface
 * @method $this inline(Asset $asset): ?AssetInterface
 * @method $this update(Asset $asset): ?AssetInterface
 */
class AssetsCollection implements AssetsCollectionInterface, AssetsResolverInterface
{
    use AssetsCollectionTrait;
    use AssetsResolverTrait;

    private $assets = [];

    public function run()
    {
        foreach ($this->assets as $group => $assets) {
            $classname = __NAMESPACE__ . '\\' . $group . 's';
            if (class_exists($classname)) {
                (new $classname($assets))->run();
            }
        }
    }

    public function __call(string $name, Asset $asset): ?AssetInterface
    {
        $asset->action = $name;
        $type          = $this->getType($asset);

        return $this->assets[$type][$asset->handle] = $asset;
    }

    private function getType(Asset $asset)
    {
        $class = explode('\\', get_class($asset));

        return end($class);
    }

    public function get(string $handle, string $group): ?Asset
    {
        $group = ucfirst($group);
        $asset = null;

        if (array_key_exists($group, $this->assets)) {
            array_walk($this->assets[$group], function ($value, $key) use (&$asset, $handle) {
                if ($key === $handle) {
                    $asset = $value;
                }
            });
        }

        return $asset;
    }
}
