<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database;

use Exception;
use Onepix\FoodSpotVendor\Illuminate\Database\Query\Grammars\MySqlGrammar as QueryGrammar;
use Onepix\FoodSpotVendor\Illuminate\Database\Query\Processors\MySqlProcessor;
use Onepix\FoodSpotVendor\Illuminate\Database\Schema\Grammars\MySqlGrammar as SchemaGrammar;
use Onepix\FoodSpotVendor\Illuminate\Database\Schema\MySqlBuilder;
use Onepix\FoodSpotVendor\Illuminate\Database\Schema\MySqlSchemaState;
use Illuminate\Filesystem\Filesystem;
use Onepix\FoodSpotVendor\Illuminate\Support\Str;
use PDO;

class MySqlConnection extends Connection
{
    /**
     * The last inserted ID generated by the server.
     *
     * @var string|int|null
     */
    protected $lastInsertId;

    /**
     * {@inheritdoc}
     */
    public function getDriverTitle()
    {
        return $this->isMaria() ? 'MariaDB' : 'MySQL';
    }

    /**
     * Run an insert statement against the database.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  string|null  $sequence
     * @return bool
     */
    public function insert($query, $bindings = [], $sequence = null)
    {
        return $this->run($query, $bindings, function ($query, $bindings) use ($sequence) {
            if ($this->pretending()) {
                return true;
            }

            $statement = $this->getPdo()->prepare($query);

            $this->bindValues($statement, $this->prepareBindings($bindings));

            $this->recordsHaveBeenModified();

            $result = $statement->execute();

            $this->lastInsertId = $this->getPdo()->lastInsertId($sequence);

            return $result;
        });
    }

    /**
     * Escape a binary value for safe SQL embedding.
     *
     * @param  string  $value
     * @return string
     */
    protected function escapeBinary($value)
    {
        $hex = bin2hex($value);

        return "x'{$hex}'";
    }

    /**
     * Determine if the given database exception was caused by a unique constraint violation.
     *
     * @param  \Exception  $exception
     * @return bool
     */
    protected function isUniqueConstraintError(Exception $exception)
    {
        return boolval(preg_match('#Integrity constraint violation: 1062#i', $exception->getMessage()));
    }

    /**
     * Get the connection's last insert ID.
     *
     * @return string|int|null
     */
    public function getLastInsertId()
    {
        return $this->lastInsertId;
    }

    /**
     * Determine if the connected database is a MariaDB database.
     *
     * @return bool
     */
    public function isMaria()
    {
        return str_contains($this->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION), 'MariaDB');
    }

    /**
     * Get the server version for the connection.
     *
     * @return string
     */
    public function getServerVersion(): string
    {
        return str_contains($version = parent::getServerVersion(), 'MariaDB')
            ? Str::between($version, '5.5.5-', '-MariaDB')
            : $version;
    }

    /**
     * Get the default query grammar instance.
     *
     * @return \Onepix\FoodSpotVendor\Illuminate\Database\Query\Grammars\MySqlGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        ($grammar = new QueryGrammar)->setConnection($this);

        return $this->withTablePrefix($grammar);
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return \Onepix\FoodSpotVendor\Illuminate\Database\Schema\MySqlBuilder
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new MySqlBuilder($this);
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return \Onepix\FoodSpotVendor\Illuminate\Database\Schema\Grammars\MySqlGrammar
     */
    protected function getDefaultSchemaGrammar()
    {
        ($grammar = new SchemaGrammar)->setConnection($this);

        return $this->withTablePrefix($grammar);
    }

    /**
     * Get the schema state for the connection.
     *
     * @param  \Illuminate\Filesystem\Filesystem|null  $files
     * @param  callable|null  $processFactory
     * @return \Onepix\FoodSpotVendor\Illuminate\Database\Schema\MySqlSchemaState
     */
    public function getSchemaState(?Filesystem $files = null, ?callable $processFactory = null)
    {
        return new MySqlSchemaState($this, $files, $processFactory);
    }

    /**
     * Get the default post processor instance.
     *
     * @return \Onepix\FoodSpotVendor\Illuminate\Database\Query\Processors\MySqlProcessor
     */
    protected function getDefaultPostProcessor()
    {
        return new MySqlProcessor;
    }
}
