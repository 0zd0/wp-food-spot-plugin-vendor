<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Db\Adapter;

use BadMethodCallException;
use InvalidArgumentException;
use Onepix\FoodSpotVendor\Phinx\Db\Action\AddColumn;
use Onepix\FoodSpotVendor\Phinx\Db\Action\AddForeignKey;
use Onepix\FoodSpotVendor\Phinx\Db\Action\AddIndex;
use Onepix\FoodSpotVendor\Phinx\Db\Action\ChangeColumn;
use Onepix\FoodSpotVendor\Phinx\Db\Action\ChangeComment;
use Onepix\FoodSpotVendor\Phinx\Db\Action\ChangePrimaryKey;
use Onepix\FoodSpotVendor\Phinx\Db\Action\DropForeignKey;
use Onepix\FoodSpotVendor\Phinx\Db\Action\DropIndex;
use Onepix\FoodSpotVendor\Phinx\Db\Action\DropTable;
use Onepix\FoodSpotVendor\Phinx\Db\Action\RemoveColumn;
use Onepix\FoodSpotVendor\Phinx\Db\Action\RenameColumn;
use Onepix\FoodSpotVendor\Phinx\Db\Action\RenameTable;
use Onepix\FoodSpotVendor\Phinx\Db\Table\Column;
use Onepix\FoodSpotVendor\Phinx\Db\Table\ForeignKey;
use Onepix\FoodSpotVendor\Phinx\Db\Table\Index;
use Onepix\FoodSpotVendor\Phinx\Db\Table\Table;

/**
 * Table prefix/suffix adapter.
 *
 * Used for inserting a prefix or suffix into table names.
 */
class TablePrefixAdapter extends AdapterWrapper implements DirectActionInterface
{
    /**
     * @inheritDoc
     */
    public function getAdapterType(): string
    {
        return 'TablePrefixAdapter';
    }

    /**
     * @inheritDoc
     */
    public function hasTable(string $tableName): bool
    {
        $adapterTableName = $this->getAdapterTableName($tableName);

        return parent::hasTable($adapterTableName);
    }

    /**
     * @inheritDoc
     */
    public function createTable(Table $table, array $columns = [], array $indexes = []): void
    {
        $adapterTable = new Table(
            $this->getAdapterTableName($table->getName()),
            $table->getOptions(),
        );
        parent::createTable($adapterTable, $columns, $indexes);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     * @return void
     */
    public function changePrimaryKey(Table $table, $newColumns): void
    {
        $adapter = $this->getAdapter();
        if (!$adapter instanceof DirectActionInterface) {
            throw new BadMethodCallException('The underlying adapter does not implement DirectActionInterface');
        }

        $adapterTable = new Table(
            $this->getAdapterTableName($table->getName()),
            $table->getOptions(),
        );
        $adapter->changePrimaryKey($adapterTable, $newColumns);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     * @return void
     */
    public function changeComment(Table $table, ?string $newComment): void
    {
        $adapter = $this->getAdapter();
        if (!$adapter instanceof DirectActionInterface) {
            throw new BadMethodCallException('The underlying adapter does not implement DirectActionInterface');
        }

        $adapterTable = new Table(
            $this->getAdapterTableName($table->getName()),
            $table->getOptions(),
        );
        $adapter->changeComment($adapterTable, $newComment);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     * @return void
     */
    public function renameTable(string $tableName, string $newTableName): void
    {
        $adapter = $this->getAdapter();
        if (!$adapter instanceof DirectActionInterface) {
            throw new BadMethodCallException('The underlying adapter does not implement DirectActionInterface');
        }

        $adapterTableName = $this->getAdapterTableName($tableName);
        $adapterNewTableName = $this->getAdapterTableName($newTableName);
        $adapter->renameTable($adapterTableName, $adapterNewTableName);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     * @return void
     */
    public function dropTable(string $tableName): void
    {
        $adapter = $this->getAdapter();
        if (!$adapter instanceof DirectActionInterface) {
            throw new BadMethodCallException('The underlying adapter does not implement DirectActionInterface');
        }
        $adapterTableName = $this->getAdapterTableName($tableName);
        $adapter->dropTable($adapterTableName);
    }

    /**
     * @inheritDoc
     */
    public function truncateTable(string $tableName): void
    {
        $adapterTableName = $this->getAdapterTableName($tableName);
        parent::truncateTable($adapterTableName);
    }

    /**
     * @inheritDoc
     */
    public function getColumns(string $tableName): array
    {
        $adapterTableName = $this->getAdapterTableName($tableName);

        return parent::getColumns($adapterTableName);
    }

    /**
     * @inheritDoc
     */
    public function hasColumn(string $tableName, string $columnName): bool
    {
        $adapterTableName = $this->getAdapterTableName($tableName);

        return parent::hasColumn($adapterTableName, $columnName);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     * @return void
     */
    public function addColumn(Table $table, Column $column): void
    {
        $adapter = $this->getAdapter();
        if (!$adapter instanceof DirectActionInterface) {
            throw new BadMethodCallException('The underlying adapter does not implement DirectActionInterface');
        }
        $adapterTableName = $this->getAdapterTableName($table->getName());
        $adapterTable = new Table($adapterTableName, $table->getOptions());
        $adapter->addColumn($adapterTable, $column);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     * @return void
     */
    public function renameColumn(string $tableName, string $columnName, string $newColumnName): void
    {
        $adapter = $this->getAdapter();
        if (!$adapter instanceof DirectActionInterface) {
            throw new BadMethodCallException('The underlying adapter does not implement DirectActionInterface');
        }
        $adapterTableName = $this->getAdapterTableName($tableName);
        $adapter->renameColumn($adapterTableName, $columnName, $newColumnName);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     * @return void
     */
    public function changeColumn(string $tableName, string $columnName, Column $newColumn): void
    {
        $adapter = $this->getAdapter();
        if (!$adapter instanceof DirectActionInterface) {
            throw new BadMethodCallException('The underlying adapter does not implement DirectActionInterface');
        }
        $adapterTableName = $this->getAdapterTableName($tableName);
        $adapter->changeColumn($adapterTableName, $columnName, $newColumn);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     * @return void
     */
    public function dropColumn(string $tableName, string $columnName): void
    {
        $adapter = $this->getAdapter();
        if (!$adapter instanceof DirectActionInterface) {
            throw new BadMethodCallException('The underlying adapter does not implement DirectActionInterface');
        }
        $adapterTableName = $this->getAdapterTableName($tableName);
        $adapter->dropColumn($adapterTableName, $columnName);
    }

    /**
     * @inheritDoc
     */
    public function hasIndex(string $tableName, string|array $columns): bool
    {
        $adapterTableName = $this->getAdapterTableName($tableName);

        return parent::hasIndex($adapterTableName, $columns);
    }

    /**
     * @inheritDoc
     */
    public function hasIndexByName(string $tableName, string $indexName): bool
    {
        $adapterTableName = $this->getAdapterTableName($tableName);

        return parent::hasIndexByName($adapterTableName, $indexName);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     * @return void
     */
    public function addIndex(Table $table, Index $index): void
    {
        $adapter = $this->getAdapter();
        if (!$adapter instanceof DirectActionInterface) {
            throw new BadMethodCallException('The underlying adapter does not implement DirectActionInterface');
        }
        $adapterTable = new Table($table->getName(), $table->getOptions());
        $adapter->addIndex($adapterTable, $index);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     * @return void
     */
    public function dropIndex(string $tableName, $columns): void
    {
        $adapter = $this->getAdapter();
        if (!$adapter instanceof DirectActionInterface) {
            throw new BadMethodCallException('The underlying adapter does not implement DirectActionInterface');
        }
        $adapterTableName = $this->getAdapterTableName($tableName);
        $adapter->dropIndex($adapterTableName, $columns);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     * @return void
     */
    public function dropIndexByName(string $tableName, string $indexName): void
    {
        $adapter = $this->getAdapter();
        if (!$adapter instanceof DirectActionInterface) {
            throw new BadMethodCallException('The underlying adapter does not implement DirectActionInterface');
        }
        $adapterTableName = $this->getAdapterTableName($tableName);
        $adapter->dropIndexByName($adapterTableName, $indexName);
    }

    /**
     * @inheritDoc
     */
    public function hasPrimaryKey(string $tableName, $columns, ?string $constraint = null): bool
    {
        $adapterTableName = $this->getAdapterTableName($tableName);

        return parent::hasPrimaryKey($adapterTableName, $columns, $constraint);
    }

    /**
     * @inheritDoc
     */
    public function hasForeignKey(string $tableName, $columns, ?string $constraint = null): bool
    {
        $adapterTableName = $this->getAdapterTableName($tableName);

        return parent::hasForeignKey($adapterTableName, $columns, $constraint);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     * @return void
     */
    public function addForeignKey(Table $table, ForeignKey $foreignKey): void
    {
        $adapter = $this->getAdapter();
        if (!$adapter instanceof DirectActionInterface) {
            throw new BadMethodCallException('The underlying adapter does not implement DirectActionInterface');
        }
        $adapterTableName = $this->getAdapterTableName($table->getName());
        $adapterTable = new Table($adapterTableName, $table->getOptions());
        $adapter->addForeignKey($adapterTable, $foreignKey);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     * @return void
     */
    public function dropForeignKey(string $tableName, array $columns, ?string $constraint = null): void
    {
        $adapter = $this->getAdapter();
        if (!$adapter instanceof DirectActionInterface) {
            throw new BadMethodCallException('The underlying adapter does not implement DirectActionInterface');
        }
        $adapterTableName = $this->getAdapterTableName($tableName);
        $adapter->dropForeignKey($adapterTableName, $columns, $constraint);
    }

    /**
     * @inheritDoc
     */
    public function insert(Table $table, array $row): void
    {
        $adapterTableName = $this->getAdapterTableName($table->getName());
        $adapterTable = new Table($adapterTableName, $table->getOptions());
        parent::insert($adapterTable, $row);
    }

    /**
     * @inheritDoc
     */
    public function bulkinsert(Table $table, array $rows): void
    {
        $adapterTableName = $this->getAdapterTableName($table->getName());
        $adapterTable = new Table($adapterTableName, $table->getOptions());
        parent::bulkinsert($adapterTable, $rows);
    }

    /**
     * Gets the table prefix.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return (string)$this->getOption('table_prefix');
    }

    /**
     * Gets the table suffix.
     *
     * @return string
     */
    public function getSuffix(): string
    {
        return (string)$this->getOption('table_suffix');
    }

    /**
     * Applies the prefix and suffix to the table name.
     *
     * @param string $tableName Table name
     * @return string
     */
    public function getAdapterTableName(string $tableName): string
    {
        return $this->getPrefix() . $tableName . $this->getSuffix();
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    public function executeActions(Table $table, array $actions): void
    {
        $adapterTableName = $this->getAdapterTableName($table->getName());
        $adapterTable = new Table($adapterTableName, $table->getOptions());

        foreach ($actions as $k => $action) {
            switch (true) {
                case $action instanceof AddColumn:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\AddColumn $action */
                    $actions[$k] = new AddColumn($adapterTable, $action->getColumn());
                    break;

                case $action instanceof AddIndex:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\AddIndex $action */
                    $actions[$k] = new AddIndex($adapterTable, $action->getIndex());
                    break;

                case $action instanceof AddForeignKey:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\AddForeignKey $action */
                    $foreignKey = clone $action->getForeignKey();
                    $refTable = $foreignKey->getReferencedTable();
                    $refTableName = $this->getAdapterTableName($refTable->getName());
                    $foreignKey->setReferencedTable(new Table($refTableName, $refTable->getOptions()));
                    $actions[$k] = new AddForeignKey($adapterTable, $foreignKey);
                    break;

                case $action instanceof ChangeColumn:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\ChangeColumn $action */
                    $actions[$k] = new ChangeColumn($adapterTable, $action->getColumnName(), $action->getColumn());
                    break;

                case $action instanceof DropForeignKey:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\DropForeignKey $action */
                    $actions[$k] = new DropForeignKey($adapterTable, $action->getForeignKey());
                    break;

                case $action instanceof DropIndex:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\DropIndex $action */
                    $actions[$k] = new DropIndex($adapterTable, $action->getIndex());
                    break;

                case $action instanceof DropTable:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\DropTable $action */
                    $actions[$k] = new DropTable($adapterTable);
                    break;

                case $action instanceof RemoveColumn:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\RemoveColumn $action */
                    $actions[$k] = new RemoveColumn($adapterTable, $action->getColumn());
                    break;

                case $action instanceof RenameColumn:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\RenameColumn $action */
                    $actions[$k] = new RenameColumn($adapterTable, $action->getColumn(), $action->getNewName());
                    break;

                case $action instanceof RenameTable:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\RenameTable $action */
                    $actions[$k] = new RenameTable($adapterTable, $this->getAdapterTableName($action->getNewName()));
                    break;

                case $action instanceof ChangePrimaryKey:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\ChangePrimaryKey $action */
                    $actions[$k] = new ChangePrimaryKey($adapterTable, $action->getNewColumns());
                    break;

                case $action instanceof ChangeComment:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\ChangeComment $action */
                    $actions[$k] = new ChangeComment($adapterTable, $action->getNewComment());
                    break;

                default:
                    throw new InvalidArgumentException(
                        sprintf("Forgot to implement table prefixing for action: '%s'", get_class($action)),
                    );
            }
        }

        parent::executeActions($adapterTable, $actions);
    }
}
