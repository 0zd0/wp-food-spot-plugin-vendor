<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Db\Action;

use Onepix\FoodSpotVendor\Phinx\Db\Table\Index;
use Onepix\FoodSpotVendor\Phinx\Db\Table\Table;

class AddIndex extends Action
{
    /**
     * The index to add to the table
     *
     * @var \Onepix\FoodSpotVendor\Phinx\Db\Table\Index
     */
    protected Index $index;

    /**
     * Constructor
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table The table to add the index to
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Index $index The index to be added
     */
    public function __construct(Table $table, Index $index)
    {
        parent::__construct($table);
        $this->index = $index;
    }

    /**
     * Creates a new AddIndex object after building the index object with the
     * provided arguments
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Table\Table $table The table to add the index to
     * @param string|string[]|\Onepix\FoodSpotVendor\Phinx\Db\Table\Index $columns The columns to index
     * @param array<string, mixed> $options Additional options for the index creation
     * @return static
     */
    public static function build(Table $table, string|array|Index $columns, array $options = []): static
    {
        // create a new index object if strings or an array of strings were supplied
        $index = $columns;

        if (!$columns instanceof Index) {
            $index = new Index();

            $index->setColumns($columns);
            $index->setOptions($options);
        }

        return new static($table, $index);
    }

    /**
     * Returns the index to be added
     *
     * @return \Onepix\FoodSpotVendor\Phinx\Db\Table\Index
     */
    public function getIndex(): Index
    {
        return $this->index;
    }
}
