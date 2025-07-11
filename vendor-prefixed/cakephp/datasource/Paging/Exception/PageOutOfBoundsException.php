<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @since         3.5.0
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Onepix\FoodSpotVendor\Cake\Datasource\Paging\Exception;

use Onepix\FoodSpotVendor\Cake\Core\Exception\CakeException;
use Onepix\FoodSpotVendor\Cake\Core\Exception\HttpErrorCodeInterface;

/**
 * Exception raised when requested page number does not exist.
 */
class PageOutOfBoundsException extends CakeException implements HttpErrorCodeInterface
{
    /**
     * @inheritDoc
     */
    protected int $_defaultCode = 404;

    /**
     * @inheritDoc
     */
    protected string $_messageTemplate = 'Page number `%s` could not be found.';
}
