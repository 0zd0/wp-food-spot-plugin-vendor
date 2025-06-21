<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Db\Adapter;

use Onepix\FoodSpotVendor\Phinx\Db\Action\AddColumn;
use Onepix\FoodSpotVendor\Phinx\Db\Action\AddForeignKey;
use Onepix\FoodSpotVendor\Phinx\Db\Action\AddIndex;
use Onepix\FoodSpotVendor\Phinx\Db\Action\CreateTable;
use Onepix\FoodSpotVendor\Phinx\Db\Action\DropForeignKey;
use Onepix\FoodSpotVendor\Phinx\Db\Action\DropIndex;
use Onepix\FoodSpotVendor\Phinx\Db\Action\DropTable;
use Onepix\FoodSpotVendor\Phinx\Db\Action\RemoveColumn;
use Onepix\FoodSpotVendor\Phinx\Db\Action\RenameColumn;
use Onepix\FoodSpotVendor\Phinx\Db\Action\RenameTable;
use Onepix\FoodSpotVendor\Phinx\Db\Plan\Intent;
use Onepix\FoodSpotVendor\Phinx\Db\Plan\Plan;
use Onepix\FoodSpotVendor\Phinx\Db\Table\Table;
use Onepix\FoodSpotVendor\Phinx\Migration\IrreversibleMigrationException;

/**
 * Phinx Proxy Adapter.
 *
 * Used for recording migration commands to automatically reverse them.
 */
class ProxyAdapter extends AdapterWrapper
{
    /**
     * @var \Onepix\FoodSpotVendor\Phinx\Db\Action\Action[]
     */
    protected array $commands = [];

    /**
     * @inheritDoc
     */
    public function getAdapterType(): string
    {
        return 'ProxyAdapter';
    }

    /**
     * @inheritDoc
     */
    public function createTable(Table $table, array $columns = [], array $indexes = []): void
    {
        $this->commands[] = new CreateTable($table);
    }

    /**
     * @inheritDoc
     */
    public function executeActions(Table $table, array $actions): void
    {
        $this->commands = array_merge($this->commands, $actions);
    }

    /**
     * Gets an array of the recorded commands in reverse.
     *
     * @throws \Onepix\FoodSpotVendor\Phinx\Migration\IrreversibleMigrationException if a command cannot be reversed.
     * @return \Onepix\FoodSpotVendor\Phinx\Db\Plan\Intent
     */
    public function getInvertedCommands(): Intent
    {
        $inverted = new Intent();

        foreach (array_reverse($this->commands) as $command) {
            switch (true) {
                case $command instanceof CreateTable:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\CreateTable $command */
                    $inverted->addAction(new DropTable($command->getTable()));
                    break;

                case $command instanceof RenameTable:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\RenameTable $command */
                    $inverted->addAction(new RenameTable(new Table($command->getNewName()), $command->getTable()->getName()));
                    break;

                case $command instanceof AddColumn:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\AddColumn $command */
                    $inverted->addAction(new RemoveColumn($command->getTable(), $command->getColumn()));
                    break;

                case $command instanceof RenameColumn:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\RenameColumn $command */
                    $column = clone $command->getColumn();
                    $name = $column->getName();
                    $column->setName($command->getNewName());
                    $inverted->addAction(new RenameColumn($command->getTable(), $column, $name));
                    break;

                case $command instanceof AddIndex:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\AddIndex $command */
                    $inverted->addAction(new DropIndex($command->getTable(), $command->getIndex()));
                    break;

                case $command instanceof AddForeignKey:
                    /** @var \Onepix\FoodSpotVendor\Phinx\Db\Action\AddForeignKey $command */
                    $inverted->addAction(new DropForeignKey($command->getTable(), $command->getForeignKey()));
                    break;

                default:
                    throw new IrreversibleMigrationException(sprintf(
                        'Cannot reverse a "%s" command',
                        get_class($command),
                    ));
            }
        }

        return $inverted;
    }

    /**
     * Execute the recorded commands in reverse.
     *
     * @return void
     */
    public function executeInvertedCommands(): void
    {
        $plan = new Plan($this->getInvertedCommands());
        $plan->executeInverse($this->getAdapter());
    }
}
