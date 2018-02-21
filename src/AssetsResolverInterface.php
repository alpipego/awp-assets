<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 21.02.2018
 * Time: 15:04
 */
declare(strict_types=1);

namespace WPHibou\Assets;

interface AssetsResolverInterface
{
    public function is_registered(string $handle = null, string $type = 'script'): bool;

    public function is_enqueued(string $handle = null, string $type = 'script'): bool;

    public function is_to_do(string $handle = null, string $type = 'script'): bool;

    public function is_done(string $handle = null, string $type = 'script'): bool;
}
