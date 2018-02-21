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
 *
 * @method Asset media(string $media = 'all')
 */
class Style extends Asset
{
    public $media = 'all';

    public function __construct($handle)
    {
        parent::__construct($handle);
    }
}
