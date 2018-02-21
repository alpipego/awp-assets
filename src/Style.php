<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05.12.2016
 * Time: 13:38
 */

namespace WPHibou\Assets;

/**
 * Class Style
 * @package WPHibou\Assets
 */
class Style extends Asset implements StyleInterface, AssetsResolverInterface
{
    public $media = 'all';

    public function __construct($handle)
    {
        parent::__construct($handle);
    }

    public function media(string $media = 'all'): StyleInterface
    {
        $this->args = $media;

        return $this;
    }
}
