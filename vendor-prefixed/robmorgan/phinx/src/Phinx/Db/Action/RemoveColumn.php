<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Db\Action;

use Onepix\FoodSpotVendor\Phinx\Db\Table\Column;
use Onepix\FoodSpotVendor\Phinx\Db\Table\Table;

class RemoveColumn extends Action
{
    /**
     * The column to be removed
     *
     * @var \Onepix\FoodSpotVendor\Phinx\Db\Table\Column
     */
    protected Column $column;

    /**
     * Constructor
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table The table where the column is
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Column $column The column to be removed
     */
    public function __construct(Table $table, Column $column)
    {
        parent::__construct($table);
        $this->column = $column;
    }

    /**
     * Creates a new RemoveColumn object after assembling the
     * passed arguments.
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table The table where the column is
     * @param string $columnName The name of the column to drop
     * @return static
     */
    public static function build(Table $table, string $columnName): static
    {
        $column = new Column();
        $column->setName($columnName);

        return new static($table, $column);
    }

    /**
     * Returns the column to be dropped
     *
     * @return \Onepix\FoodSpotVendor\Phinx\Db\Table\Column
     */
    public function getColumn(): Column
    {
        return $this->column;
    }
}
