<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Migration;

use Onepix\FoodSpotVendor\Cake\Database\Query;
use Onepix\FoodSpotVendor\Cake\Database\Query\DeleteQuery;
use Onepix\FoodSpotVendor\Cake\Database\Query\InsertQuery;
use Onepix\FoodSpotVendor\Cake\Database\Query\SelectQuery;
use Onepix\FoodSpotVendor\Cake\Database\Query\UpdateQuery;
use Onepix\FoodSpotVendor\Phinx\Db\Adapter\AdapterInterface;
use Onepix\FoodSpotVendor\Phinx\Db\Table;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface;

/**
 * Migration interface
 */
interface MigrationInterface
{
    /**
     * @var string
     */
    public const CHANGE = 'change';

    /**
     * @var string
     */
    public const UP = 'up';

    /**
     * @var string
     */
    public const DOWN = 'down';

    /**
     * @var string
     */
    public const INIT = 'init';

    /**
     * Sets the database adapter.
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Adapter\AdapterInterface $adapter Database Adapter
     * @return $this
     */
    public function setAdapter(AdapterInterface $adapter);

    /**
     * Gets the database adapter.
     *
     * @return \Onepix\FoodSpotVendor\Phinx\Db\Adapter\AdapterInterface|null
     */
    public function getAdapter(): ?AdapterInterface;

    /**
     * Sets the input object to be used in migration object
     *
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface $input Input
     * @return $this
     */
    public function setInput(InputInterface $input);

    /**
     * Gets the input object to be used in migration object
     *
     * @return \Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface|null
     */
    public function getInput(): ?InputInterface;

    /**
     * Sets the output object to be used in migration object
     *
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface $output Output
     * @return $this
     */
    public function setOutput(OutputInterface $output);

    /**
     * Gets the output object to be used in migration object
     *
     * @return \Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface|null
     */
    public function getOutput(): ?OutputInterface;

    /**
     * Gets the name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Gets the detected environment
     *
     * @return string
     */
    public function getEnvironment(): string;

    /**
     * Sets the migration version number.
     *
     * @param int $version Version
     * @return $this
     */
    public function setVersion(int $version);

    /**
     * Gets the migration version number.
     *
     * @return int
     */
    public function getVersion(): int;

    /**
     * Sets whether this migration is being applied or reverted
     *
     * @param bool $isMigratingUp True if the migration is being applied
     * @return $this
     */
    public function setMigratingUp(bool $isMigratingUp);

    /**
     * Gets whether this migration is being applied or reverted.
     * True means that the migration is being applied.
     *
     * @return bool
     */
    public function isMigratingUp(): bool;

    /**
     * Executes a SQL statement and returns the number of affected rows.
     *
     * @param string $sql SQL
     * @param array $params parameters to use for prepared query
     * @return int
     */
    public function execute(string $sql, array $params = []): int;

    /**
     * Executes a SQL statement.
     *
     * The return type depends on the underlying adapter being used. To improve
     * IDE auto-completion possibility, you can overwrite the query method
     * phpDoc in your (typically custom abstract parent) migration class, where
     * you can set the return type by the adapter in your current use.
     *
     * @param string $sql SQL
     * @param array $params parameters to use for prepared query
     * @return mixed
     */
    public function query(string $sql, array $params = []): mixed;

    /**
     * Returns a new Query object that can be used to build complex SELECT, UPDATE, INSERT or DELETE
     * queries and execute them against the current database.
     *
     * Queries executed through the query builder are always sent to the database, regardless of the
     * the dry-run settings.
     *
     * @see https://api.cakephp.org/3.6/class-Cake.Database.Query.html
     * @param string $type Query
     * @return \Onepix\FoodSpotVendor\Cake\Database\Query
     */
    public function getQueryBuilder(string $type): Query;

    /**
     * Returns a new SelectQuery object that can be used to build complex
     * SELECT queries and execute them against the current database.
     *
     * Queries executed through the query builder are always sent to the database, regardless of the
     * the dry-run settings.
     *
     * @return \Onepix\FoodSpotVendor\Cake\Database\Query\SelectQuery
     */
    public function getSelectBuilder(): SelectQuery;

    /**
     * Returns a new InsertQuery object that can be used to build complex
     * INSERT queries and execute them against the current database.
     *
     * Queries executed through the query builder are always sent to the database, regardless of the
     * the dry-run settings.
     *
     * @return \Onepix\FoodSpotVendor\Cake\Database\Query\InsertQuery
     */
    public function getInsertBuilder(): InsertQuery;

    /**
     * Returns a new UpdateQuery object that can be used to build complex
     * UPDATE queries and execute them against the current database.
     *
     * Queries executed through the query builder are always sent to the database, regardless of the
     * the dry-run settings.
     *
     * @return \Onepix\FoodSpotVendor\Cake\Database\Query\UpdateQuery
     */
    public function getUpdateBuilder(): UpdateQuery;

    /**
     * Returns a new DeleteQuery object that can be used to build complex
     * DELETE queries and execute them against the current database.
     *
     * Queries executed through the query builder are always sent to the database, regardless of the
     * the dry-run settings.
     *
     * @return \Onepix\FoodSpotVendor\Cake\Database\Query\DeleteQuery
     */
    public function getDeleteBuilder(): DeleteQuery;

    /**
     * Executes a query and returns only one row as an array.
     *
     * @param string $sql SQL
     * @return array|false
     */
    public function fetchRow(string $sql): array|false;

    /**
     * Executes a query and returns an array of rows.
     *
     * @param string $sql SQL
     * @return array
     */
    public function fetchAll(string $sql): array;

    /**
     * Create a new database.
     *
     * @param string $name Database Name
     * @param array<string, mixed> $options Options
     * @return void
     */
    public function createDatabase(string $name, array $options): void;

    /**
     * Drop a database.
     *
     * @param string $name Database Name
     * @return void
     */
    public function dropDatabase(string $name): void;

    /**
     * Creates schema.
     *
     * This will thrown an error for adapters that do not support schemas.
     *
     * @param string $name Schema name
     * @return void
     * @throws \BadMethodCallException
     */
    public function createSchema(string $name): void;

    /**
     * Drops schema.
     *
     * This will thrown an error for adapters that do not support schemas.
     *
     * @param string $name Schema name
     * @return void
     * @throws \BadMethodCallException
     */
    public function dropSchema(string $name): void;

    /**
     * Checks to see if a table exists.
     *
     * @param string $tableName Table name
     * @return bool
     */
    public function hasTable(string $tableName): bool;

    /**
     * Returns an instance of the <code>\Table</code> class.
     *
     * You can use this class to create and manipulate tables.
     *
     * @param string $tableName Table name
     * @param array<string, mixed> $options Options
     * @return \Onepix\FoodSpotVendor\Phinx\Db\Table
     */
    public function table(string $tableName, array $options): Table;

    /**
     * Perform checks on the migration, printing a warning
     * if there are potential problems.
     *
     * @return void
     */
    public function preFlightCheck(): void;

    /**
     * Perform checks on the migration after completion
     *
     * Right now, the only check is whether all changes were committed
     *
     * @return void
     */
    public function postFlightCheck(): void;

    /**
     * Checks to see if the migration should be executed.
     *
     * Returns true by default.
     *
     * You can use this to prevent a migration from executing.
     *
     * @return bool
     */
    public function shouldExecute(): bool;
}
