<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 21.02.2018
 * Time: 15:04
 */
declare(strict_types = 1);

namespace Alpipego\AWP\Assets;

interface AssetsResolverInterface
{
    public function is_registered(): bool;

    public function is_enqueued(): bool;

    public function is_to_do(): bool;

    public function is_done(): bool;

    public function is_inlined(): bool;

    public function done();
    
    public function to_do();
    
    public function state(): array;
}
