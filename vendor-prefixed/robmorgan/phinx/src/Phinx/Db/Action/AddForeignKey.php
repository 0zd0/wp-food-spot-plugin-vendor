<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Db\Action;

use Onepix\FoodSpotVendor\Phinx\Db\Table\ForeignKey;
use Onepix\FoodSpotVendor\Phinx\Db\Table\Table;

class AddForeignKey extends Action
{
    /**
     * The foreign key to add
     *
     * @var \Onepix\FoodSpotVendor\Phinx\Db\Table\ForeignKey
     */
    protected ForeignKey $foreignKey;

    /**
     * Constructor
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table The table to add the foreign key to
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\ForeignKey $fk The foreign key to add
     */
    public function __construct(Table $table, ForeignKey $fk)
    {
        parent::__construct($table);
        $this->foreignKey = $fk;
    }

    /**
     * Creates a new AddForeignKey object after building the foreign key with
     * the passed attributes
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table The table object to add the foreign key to
     * @param string|string[] $columns The columns for the foreign key
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table|string $referencedTable The table the foreign key references
     * @param string|string[] $referencedColumns The columns in the referenced table
     * @param array<string, mixed> $options Extra options for the foreign key
     * @param string|null $name The name of the foreign key
     * @return static
     */
    public static function build(Table $table, string|array $columns, Table|string $referencedTable, string|array $referencedColumns = ['id'], array $options = [], ?string $name = null): static
    {
        if (is_string($referencedColumns)) {
            $referencedColumns = [$referencedColumns]; // str to array
        }

        if (is_string($referencedTable)) {
            $referencedTable = new Table($referencedTable);
        }

        $fk = new ForeignKey();
        $fk->setReferencedTable($referencedTable)
           ->setColumns($columns)
           ->setReferencedColumns($referencedColumns)
           ->setOptions($options);

        if ($name !== null) {
            $fk->setConstraint($name);
        }

        return new static($table, $fk);
    }

    /**
     * Returns the foreign key to be added
     *
     * @return \Onepix\FoodSpotVendor\Phinx\Db\Table\ForeignKey
     */
    public function getForeignKey(): ForeignKey
    {
        return $this->foreignKey;
    }
}
