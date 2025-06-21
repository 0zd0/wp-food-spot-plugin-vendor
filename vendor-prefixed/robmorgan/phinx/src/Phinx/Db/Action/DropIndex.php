<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Db\Action;

use Onepix\FoodSpotVendor\Phinx\Db\Table\Index;
use Onepix\FoodSpotVendor\Phinx\Db\Table\Table;

class DropIndex extends Action
{
    /**
     * The index to drop
     *
     * @var \Onepix\FoodSpotVendor\Phinx\Db\Table\Index
     */
    protected Index $index;

    /**
     * Constructor
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table The table owning the index
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Index $index The index to be dropped
     */
    public function __construct(Table $table, Index $index)
    {
        parent::__construct($table);
        $this->index = $index;
    }

    /**
     * Creates a new DropIndex object after assembling the passed
     * arguments.
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table The table where the index is
     * @param string[] $columns the indexed columns
     * @return static
     */
    public static function build(Table $table, array $columns = []): static
    {
        $index = new Index();
        $index->setColumns($columns);

        return new static($table, $index);
    }

    /**
     * Creates a new DropIndex when the name of the index to drop
     * is known.
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table The table where the index is
     * @param string $name The name of the index
     * @return static
     */
    public static function buildFromName(Table $table, string $name): static
    {
        $index = new Index();
        $index->setName($name);

        return new static($table, $index);
    }

    /**
     * Returns the index to be dropped
     *
     * @return \Onepix\FoodSpotVendor\Phinx\Db\Table\Index
     */
    public function getIndex(): Index
    {
        return $this->index;
    }
}
