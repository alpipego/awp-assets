<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 21.02.2018
 * Time: 12:50
 */
declare(strict_types=1);

namespace Alpipego\AWP\Assets;

interface StyleInterface extends AssetInterface
{
    public function media(string $media = 'screen'): StyleInterface;
}
