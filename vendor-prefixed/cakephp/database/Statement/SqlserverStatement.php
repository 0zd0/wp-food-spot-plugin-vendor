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
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Onepix\FoodSpotVendor\Cake\Database\Statement;

use PDO;

/**
 * Statement class meant to be used by an Sqlserver driver
 *
 * @internal
 */
class SqlserverStatement extends Statement
{
    /**
     * @inheritDoc
     */
    protected function performBind(string|int $column, mixed $value, int $type): void
    {
        if ($type === PDO::PARAM_LOB) {
            $this->statement->bindParam($column, $value, $type, 0, PDO::SQLSRV_ENCODING_BINARY);
        } else {
            parent::performBind($column, $value, $type);
        }
    }
}
