<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 06.12.2016
 * Time: 09:41
 */
declare(strict_types = 1);

namespace Alpipego\AWP\Assets;

/**
 * Class AssetsCollection
 * @package Alpipego\AWP\Assets
 *
 * @method AssetInterface add(AssetInterface $asset)
 * @method AssetInterface remove(AssetInterface $asset)
 * @method AssetInterface inline(AssetInterface $asset)
 * @method AssetInterface update(AssetInterface $asset)
 */
class AssetsCollection implements AssetsCollectionInterface
{
    use AssetsCollectionTrait;

    private $assets = [];
    private $type;

    public function __construct($type = 'wp')
    {
        $this->type = $type;
    }

    public function run()
    {
        array_walk($this->assets, function (array $assets, string $group) {
            $classname = __NAMESPACE__ . '\\' . $group . 's';
            if (class_exists($classname)) {
                /** @var AbstractAssets $asset */
                $asset = new $classname($assets, $this->type);
                $asset->run();
            }
        });
    }

    public function __call(string $name, Asset $asset): ?AssetInterface
    {
        $asset->action = $name;
        $type          = $this->getType($asset);

        return $this->assets[$type][$asset->handle] = $asset;
    }

    private function getType(Asset $asset): string
    {
        $ref   = new \ReflectionClass($asset);
        $class = $ref->name;
        if ($ref->hasConstant('UPDATER') || $ref->getConstant('UPDATER')) {
            $class = $ref->getParentClass()->name;
        }
        $class = explode('\\', $class);

        return end($class);
    }

    public function get(string $handle, string $group = 'script'): ?Asset
    {
        $group = ucfirst($group);

        if ($this->arrayKeyExistsRecursive($this->assets, $group, $handle)) {
            return $this->assets[$group][$handle];
        }

        return null;
    }

    private function arrayKeyExistsRecursive(array $array, string ...$keys): bool
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                return false;
            }
            $array = $array[$key];
        }

        return true;
    }
}
