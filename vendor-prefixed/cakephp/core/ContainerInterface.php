<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         4.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Onepix\FoodSpotVendor\Cake\Core;

use Onepix\FoodSpotVendor\League\Container\DefinitionContainerInterface;
use Onepix\FoodSpotVendor\Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * Interface for the Dependency Injection Container in CakePHP applications
 *
 * This interface extends the PSR-11 container interface and adds
 * methods to add services and service providers to the container.
 *
 * The methods defined in this interface use the conventions provided
 * by league/container as that is the library that CakePHP uses.
 */
interface ContainerInterface extends DefinitionContainerInterface
{
    /**
     * @param \Onepix\FoodSpotVendor\Psr\Container\ContainerInterface $container The container instance to use as delegation
     * @return \Onepix\FoodSpotVendor\Psr\Container\ContainerInterface
     */
    public function delegate(PsrContainerInterface $container): PsrContainerInterface;
}
