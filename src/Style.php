<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05.12.2016
 * Time: 13:38
 */
declare(strict_types=1);

namespace Alpipego\AWP\Assets;

final class Style extends Asset implements StyleInterface, AssetsResolverInterface
{
    public $media = 'screen';

    public function __construct($handle)
    {
        parent::__construct($handle);
    }

    public function media(string $media = 'screen'): StyleInterface
    {
        $this->args = $media;

        return $this->__call(__FUNCTION__, [$media]);
    }
}
