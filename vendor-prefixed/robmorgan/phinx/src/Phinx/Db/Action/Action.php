<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Db\Action;

use Onepix\FoodSpotVendor\Phinx\Db\Table\Table;

abstract class Action
{
    /**
     * @var \Onepix\FoodSpotVendor\Phinx\Db\Table\Table
     */
    protected Table $table;

    /**
     * Constructor
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table the Table to apply the action to
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * The table this action will be applied to
     *
     * @return \Onepix\FoodSpotVendor\Phinx\Db\Table\Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }
}
