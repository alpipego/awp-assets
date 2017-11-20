<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05.12.2016
 * Time: 13:38
 */

namespace WPHibou\Assets;

/**
 * Class Script
 * @package WPHibou\Assets
 *
 */
class Script extends Asset
{
    public $in_footer = true;

    public function __construct($handle)
    {
        parent::__construct($handle);
    }
}
