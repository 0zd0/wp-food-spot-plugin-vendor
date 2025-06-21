<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Db\Action;

use Onepix\FoodSpotVendor\Phinx\Db\Table\Column;
use Onepix\FoodSpotVendor\Phinx\Db\Table\Table;
use Onepix\FoodSpotVendor\Phinx\Util\Literal;

class AddColumn extends Action
{
    /**
     * The column to add
     *
     * @var \Onepix\FoodSpotVendor\Phinx\Db\Table\Column
     */
    protected Column $column;

    /**
     * Constructor
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table The table to add the column to
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Column $column The column to add
     */
    public function __construct(Table $table, Column $column)
    {
        parent::__construct($table);
        $this->column = $column;
    }

    /**
     * Returns a new AddColumn object after assembling the given commands
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table The table to add the column to
     * @param string $columnName The column name
     * @param string|\Onepix\FoodSpotVendor\Phinx\Util\Literal $type The column type
     * @param array<string, mixed> $options The column options
     * @return static
     */
    public static function build(Table $table, string $columnName, string|Literal $type, array $options = []): static
    {
        $column = new Column();
        $column->setName($columnName);
        $column->setType($type);
        $column->setOptions($options); // map options to column methods

        return new static($table, $column);
    }

    /**
     * Returns the column to be added
     *
     * @return \Onepix\FoodSpotVendor\Phinx\Db\Table\Column
     */
    public function getColumn(): Column
    {
        return $this->column;
    }
}
