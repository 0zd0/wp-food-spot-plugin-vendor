<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Symfony\Component\Cache\Adapter;

use Onepix\FoodSpotVendor\Symfony\Component\Cache\Marshaller\DefaultMarshaller;
use Onepix\FoodSpotVendor\Symfony\Component\Cache\Marshaller\MarshallerInterface;
use Onepix\FoodSpotVendor\Symfony\Component\Cache\PruneableInterface;
use Onepix\FoodSpotVendor\Symfony\Component\Cache\Traits\FilesystemTrait;

class FilesystemAdapter extends AbstractAdapter implements PruneableInterface
{
    use FilesystemTrait;

    public function __construct(string $namespace = '', int $defaultLifetime = 0, ?string $directory = null, ?MarshallerInterface $marshaller = null)
    {
        $this->marshaller = $marshaller ?? new DefaultMarshaller();
        parent::__construct('', $defaultLifetime);
        $this->init($namespace, $directory);
    }
}
