<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 21.02.2018
 * Time: 12:50
 */
declare(strict_types=1);

namespace Alpipego\Assets;

interface ScriptInterface extends AssetInterface
{
    public function in_footer(bool $in_footer = false): ScriptInterface;
}
