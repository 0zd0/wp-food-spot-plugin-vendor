<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database\Console;

use Illuminate\Console\Command;
use Onepix\FoodSpotVendor\Illuminate\Database\ConnectionInterface;
use Onepix\FoodSpotVendor\Illuminate\Support\Arr;

abstract class DatabaseInspectionCommand extends Command
{
    /**
     * Get a human-readable name for the given connection.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Database\ConnectionInterface  $connection
     * @param  string  $database
     * @return string
     *
     * @deprecated
     */
    protected function getConnectionName(ConnectionInterface $connection, $database)
    {
        return $connection->getDriverTitle();
    }

    /**
     * Get the number of open connections for a database.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Database\ConnectionInterface  $connection
     * @return int|null
     *
     * @deprecated
     */
    protected function getConnectionCount(ConnectionInterface $connection)
    {
        return $connection->threadCount();
    }

    /**
     * Get the connection configuration details for the given connection.
     *
     * @param  string|null  $database
     * @return array
     */
    protected function getConfigFromDatabase($database)
    {
        $database ??= config('database.default');

        return Arr::except(config('database.connections.'.$database), ['password']);
    }

    /**
     * Remove the table prefix from a table name, if it exists.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Database\ConnectionInterface  $connection
     * @param  string  $table
     * @return string
     */
    protected function withoutTablePrefix(ConnectionInterface $connection, string $table)
    {
        $prefix = $connection->getTablePrefix();

        return str_starts_with($table, $prefix)
            ? substr($table, strlen($prefix))
            : $table;
    }
}
