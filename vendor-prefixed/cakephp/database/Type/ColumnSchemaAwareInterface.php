<?php
declare(strict_types=1);

namespace Onepix\FoodSpotVendor\Cake\Database\Type;

use Onepix\FoodSpotVendor\Cake\Database\Driver;
use Onepix\FoodSpotVendor\Cake\Database\Schema\TableSchemaInterface;

interface ColumnSchemaAwareInterface
{
    /**
     * Generate the SQL fragment for a single column in a table.
     *
     * @param \Onepix\FoodSpotVendor\Cake\Database\Schema\TableSchemaInterface $schema The table schema instance the column is in.
     * @param string $column The name of the column.
     * @param \Onepix\FoodSpotVendor\Cake\Database\Driver $driver The driver instance being used.
     * @return string|null An SQL fragment, or `null` in case the column isn't processed by this type.
     */
    public function getColumnSql(TableSchemaInterface $schema, string $column, Driver $driver): ?string;

    /**
     * Convert a SQL column definition to an abstract type definition.
     *
     * @param array $definition The column definition.
     * @param \Onepix\FoodSpotVendor\Cake\Database\Driver $driver The driver instance being used.
     * @return array<string, mixed>|null Array of column information, or `null` in case the column isn't processed by this type.
     */
    public function convertColumnDefinition(array $definition, Driver $driver): ?array;
}
