<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Db\Action;

use Onepix\FoodSpotVendor\Phinx\Db\Table\ForeignKey;
use Onepix\FoodSpotVendor\Phinx\Db\Table\Table;

class DropForeignKey extends Action
{
    /**
     * The foreign key to remove
     *
     * @var \Onepix\FoodSpotVendor\Phinx\Db\Table\ForeignKey
     */
    protected ForeignKey $foreignKey;

    /**
     * Constructor
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table The table to remove the constraint from
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\ForeignKey $foreignKey The foreign key to remove
     */
    public function __construct(Table $table, ForeignKey $foreignKey)
    {
        parent::__construct($table);
        $this->foreignKey = $foreignKey;
    }

    /**
     * Creates a new DropForeignKey object after building the ForeignKey
     * definition out of the passed arguments.
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table The table to delete the foreign key from
     * @param string|string[] $columns The columns participating in the foreign key
     * @param string|null $constraint The constraint name
     * @return static
     */
    public static function build(Table $table, string|array $columns, ?string $constraint = null): static
    {
        if (is_string($columns)) {
            $columns = [$columns];
        }

        $foreignKey = new ForeignKey();
        $foreignKey->setColumns($columns);

        if ($constraint) {
            $foreignKey->setConstraint($constraint);
        }

        return new static($table, $foreignKey);
    }

    /**
     * Returns the foreign key to remove
     *
     * @return \Onepix\FoodSpotVendor\Phinx\Db\Table\ForeignKey
     */
    public function getForeignKey(): ForeignKey
    {
        return $this->foreignKey;
    }
}
