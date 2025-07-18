<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Db\Action;

use Onepix\FoodSpotVendor\Phinx\Db\Table\Column;
use Onepix\FoodSpotVendor\Phinx\Db\Table\Table;

class RenameColumn extends Action
{
    /**
     * The column to be renamed
     *
     * @var \Onepix\FoodSpotVendor\Phinx\Db\Table\Column
     */
    protected Column $column;

    /**
     * The new name for the column
     *
     * @var string
     */
    protected string $newName;

    /**
     * Constructor
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table The table where the column is
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Column $column The column to be renamed
     * @param string $newName The new name for the column
     */
    public function __construct(Table $table, Column $column, string $newName)
    {
        parent::__construct($table);
        $this->newName = $newName;
        $this->column = $column;
    }

    /**
     * Creates a new RenameColumn object after building the passed
     * arguments
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table The table where the column is
     * @param string $columnName The name of the column to be changed
     * @param string $newName The new name for the column
     * @return static
     */
    public static function build(Table $table, string $columnName, string $newName): static
    {
        $column = new Column();
        $column->setName($columnName);

        return new static($table, $column, $newName);
    }

    /**
     * Returns the column to be changed
     *
     * @return \Onepix\FoodSpotVendor\Phinx\Db\Table\Column
     */
    public function getColumn(): Column
    {
        return $this->column;
    }

    /**
     * Returns the new name for the column
     *
     * @return string
     */
    public function getNewName(): string
    {
        return $this->newName;
    }
}
