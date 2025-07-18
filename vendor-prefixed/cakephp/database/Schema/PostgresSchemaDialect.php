<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Onepix\FoodSpotVendor\Cake\Database\Schema;

use Onepix\FoodSpotVendor\Cake\Database\Exception\DatabaseException;

/**
 * Schema management/reflection features for Postgres.
 *
 * @internal
 */
class PostgresSchemaDialect extends SchemaDialect
{
    /**
     * @const int
     */
    public const DEFAULT_SRID = 4326;

    /**
     * Generate the SQL to list the tables and views.
     *
     * @param array<string, mixed> $config The connection configuration to use for
     *    getting tables from.
     * @return array An array of (sql, params) to execute.
     */
    public function listTablesSql(array $config): array
    {
        $sql = 'SELECT table_name as name FROM information_schema.tables
                WHERE table_schema = ? ORDER BY name';
        $schema = $config['schema'] ?? 'public';

        return [$sql, [$schema]];
    }

    /**
     * Generate the SQL to list the tables, excluding all views.
     *
     * @param array<string, mixed> $config The connection configuration to use for
     *    getting tables from.
     * @return array<mixed> An array of (sql, params) to execute.
     */
    public function listTablesWithoutViewsSql(array $config): array
    {
        $sql = 'SELECT table_name as name FROM information_schema.tables
                WHERE table_schema = ? AND table_type = \'BASE TABLE\' ORDER BY name';
        $schema = $config['schema'] ?? 'public';

        return [$sql, [$schema]];
    }

    /**
     * @inheritDoc
     */
    public function describeColumnSql(string $tableName, array $config): array
    {
        $sql = $this->describeColumnQuery();
        $schema = $config['schema'] ?? 'public';

        return [$sql, [$tableName, $schema, $config['database']]];
    }

    /**
     * Helper method for creating SQL to describe columns in a table.
     *
     * @return string SQL to reflect columns
     */
    private function describeColumnQuery(): string
    {
        return 'SELECT DISTINCT table_schema AS schema,
            column_name AS name,
            data_type AS type,
            udt_name,
            is_identity,
            is_nullable AS null,
            column_default AS default,
            character_maximum_length AS char_length,
            c.collation_name,
            d.description as comment,
            ordinal_position,
            c.datetime_precision,
            c.numeric_precision as column_precision,
            c.numeric_scale as column_scale,
            c.identity_generation,
            pg_get_serial_sequence(attr.attrelid::regclass::text, attr.attname) IS NOT NULL AS has_serial
        FROM information_schema.columns c
        INNER JOIN pg_catalog.pg_namespace ns ON (ns.nspname = table_schema)
        INNER JOIN pg_catalog.pg_class cl ON (cl.relnamespace = ns.oid AND cl.relname = table_name)
        LEFT JOIN pg_catalog.pg_index i ON (i.indrelid = cl.oid AND i.indkey[0] = c.ordinal_position)
        LEFT JOIN pg_catalog.pg_description d on (cl.oid = d.objoid AND d.objsubid = c.ordinal_position)
        LEFT JOIN pg_catalog.pg_attribute attr ON (cl.oid = attr.attrelid AND column_name = attr.attname)
        WHERE table_name = ? AND table_schema = ? AND table_catalog = ?
        ORDER BY ordinal_position';
    }

    /**
     * Convert a column definition to the abstract types.
     *
     * The returned type will be a type that
     * Cake\Database\TypeFactory can handle.
     *
     * @param string $column The column type + length
     * @throws \Onepix\FoodSpotVendor\Cake\Database\Exception\DatabaseException when column cannot be parsed.
     * @return array<string, mixed> Array of column information.
     */
    protected function _convertColumn(string $column): array
    {
        preg_match('/([a-z\s]+)(?:\(([a-z0-9,]+)(?:,\s*([0-9]+))?\))?/i', $column, $matches);
        if (!$matches) {
            throw new DatabaseException(sprintf('Unable to parse column type from `%s`', $column));
        }

        $col = strtolower($matches[1]);
        $length = null;
        $precision = null;
        $scale = null;
        if (isset($matches[2])) {
            $length = (int)$matches[2];
        }

        $type = $this->_applyTypeSpecificColumnConversion(
            $col,
            compact('length', 'precision', 'scale'),
        );
        if ($type !== null) {
            return $type;
        }

        if (in_array($col, ['date', 'time', 'boolean'], true)) {
            return ['type' => $col, 'length' => null];
        }
        if (in_array($col, ['timestamptz', 'timestamp with time zone'], true)) {
            return ['type' => TableSchemaInterface::TYPE_TIMESTAMP_TIMEZONE, 'length' => null];
        }
        if (str_contains($col, 'timestamp')) {
            return ['type' => TableSchemaInterface::TYPE_TIMESTAMP_FRACTIONAL, 'length' => null];
        }
        if (str_contains($col, 'time')) {
            return ['type' => TableSchemaInterface::TYPE_TIME, 'length' => null];
        }
        if ($col === 'serial' || $col === 'integer') {
            return ['type' => TableSchemaInterface::TYPE_INTEGER, 'length' => 10];
        }
        if ($col === 'bigserial' || $col === 'bigint') {
            return ['type' => TableSchemaInterface::TYPE_BIGINTEGER, 'length' => 20];
        }
        if ($col === 'smallint') {
            return ['type' => TableSchemaInterface::TYPE_SMALLINTEGER, 'length' => 5];
        }
        if ($col === 'inet') {
            return ['type' => TableSchemaInterface::TYPE_STRING, 'length' => 39];
        }
        if ($col === 'uuid') {
            return ['type' => TableSchemaInterface::TYPE_UUID, 'length' => null];
        }
        if ($col === 'char') {
            return ['type' => TableSchemaInterface::TYPE_CHAR, 'length' => $length];
        }
        if (str_contains($col, 'character')) {
            return ['type' => TableSchemaInterface::TYPE_STRING, 'length' => $length];
        }
        // money is 'string' as it includes arbitrary text content
        // before the number value.
        if (str_contains($col, 'money') || $col === 'string') {
            return ['type' => TableSchemaInterface::TYPE_STRING, 'length' => $length];
        }
        if (str_contains($col, 'text')) {
            return ['type' => TableSchemaInterface::TYPE_TEXT, 'length' => null];
        }
        if ($col === 'bytea') {
            return ['type' => TableSchemaInterface::TYPE_BINARY, 'length' => null];
        }
        if ($col === 'real' || str_contains($col, 'double')) {
            return ['type' => TableSchemaInterface::TYPE_FLOAT, 'length' => null];
        }
        if (str_contains($col, 'numeric') || str_contains($col, 'decimal')) {
            return ['type' => TableSchemaInterface::TYPE_DECIMAL, 'length' => null];
        }
        if (str_contains($col, 'json')) {
            return ['type' => TableSchemaInterface::TYPE_JSON, 'length' => null];
        }
        if ($col === 'geography') {
            $srid = (int)($matches[3] ?? self::DEFAULT_SRID);
            $type = strtolower($matches[2] ?? 'point');

            return ['type' => $type, 'length' => null, 'srid' => $srid];
        }

        $length = is_numeric($length) ? $length : null;

        return ['type' => TableSchemaInterface::TYPE_STRING, 'length' => $length];
    }

    /**
     * @inheritDoc
     */
    public function convertColumnDescription(TableSchema $schema, array $row): void
    {
        $field = $this->_convertColumn($row['type']);

        if ($field['type'] === TableSchemaInterface::TYPE_BOOLEAN) {
            if ($row['default'] === 'true') {
                $row['default'] = 1;
            }
            if ($row['default'] === 'false') {
                $row['default'] = 0;
            }
        }
        if (!empty($row['has_serial'])) {
            $field['autoIncrement'] = true;
        }

        $field += [
            'default' => $this->_defaultValue($row['default']),
            'null' => $row['null'] === 'YES',
            'collate' => $row['collation_name'],
            'comment' => $row['comment'],
        ];
        $field['length'] = $row['char_length'] ?: $field['length'];

        if ($field['type'] === 'numeric' || $field['type'] === 'decimal') {
            $field['length'] = $row['column_precision'];
            $field['precision'] = $row['column_scale'] ?: null;
        }

        if ($field['type'] === TableSchemaInterface::TYPE_TIMESTAMP_FRACTIONAL) {
            $field['precision'] = $row['datetime_precision'];
            if ($field['precision'] === 0) {
                $field['type'] = TableSchemaInterface::TYPE_TIMESTAMP;
            }
        }

        if ($field['type'] === TableSchemaInterface::TYPE_TIMESTAMP_TIMEZONE) {
            $field['precision'] = $row['datetime_precision'];
        }

        $schema->addColumn($row['name'], $field);
    }

    /**
     * Split a tablename into a tuple of schema, table
     * If the table does not have a schema name included, the connection
     * schema will be used.
     *
     * @param string $tableName The table name to split
     * @param array $config Additional configuration data
     * @return array A tuple of [schema, tablename]
     */
    private function splitTablename(string $tableName, array $config = []): array
    {
        if (str_contains($tableName, '.')) {
            return explode('.', $tableName);
        }
        $driverConfig = $this->_driver->config();
        $schema = $config['schema'] ?? $driverConfig['schema'] ?? 'public';

        return [$schema, $tableName];
    }

    /**
     * @inheritDoc
     */
    public function describeColumns(string $tableName): array
    {
        $config = $this->_driver->config();
        [$schema, $name] = $this->splitTablename($tableName);

        $sql = $this->describeColumnQuery();
        $statement = $this->_driver->execute($sql, [$name, $schema, $config['database']]);
        $columns = [];
        foreach ($statement->fetchAll('assoc') as $row) {
            $field = $this->_convertColumn($row['type']);
            if ($field['type'] === TableSchemaInterface::TYPE_BOOLEAN) {
                if ($row['default'] === 'true') {
                    $row['default'] = 1;
                } elseif ($row['default'] === 'false') {
                    $row['default'] = 0;
                }
            }
            if (!empty($row['has_serial'])) {
                $field['autoIncrement'] = true;
            }

            $field += [
                'name' => $row['name'],
                'default' => $this->_defaultValue($row['default']),
                'null' => $row['null'] === 'YES',
                'collate' => $row['collation_name'],
                'comment' => $row['comment'],
            ];
            $field['length'] = $row['char_length'] ?: $field['length'];

            if ($field['type'] === 'numeric' || $field['type'] === 'decimal') {
                $field['length'] = $row['column_precision'];
                $field['precision'] = $row['column_scale'] ?: null;
            }

            if ($field['type'] === TableSchemaInterface::TYPE_TIMESTAMP_FRACTIONAL) {
                $field['precision'] = $row['datetime_precision'];
                if ($field['precision'] === 0) {
                    $field['type'] = TableSchemaInterface::TYPE_TIMESTAMP;
                }
            }

            if ($field['type'] === TableSchemaInterface::TYPE_TIMESTAMP_TIMEZONE) {
                $field['precision'] = $row['datetime_precision'];
            }
            if (isset($row['identity_generation']) && $row['identity_generation']) {
                $field['generated'] = $row['identity_generation'];
            }

            $columns[] = $field;
        }

        return $columns;
    }

    /**
     * Manipulate the default value.
     *
     * Postgres includes sequence data and casting information in default values.
     * We need to remove those.
     *
     * @param string|int|null $default The default value.
     * @return string|int|null
     */
    protected function _defaultValue(string|int|null $default): string|int|null
    {
        if (is_numeric($default) || $default === null) {
            return $default;
        }
        // Sequences
        if (str_starts_with($default, 'nextval')) {
            return null;
        }

        if (str_starts_with($default, 'NULL::')) {
            return null;
        }

        // Remove quotes and postgres casts
        return preg_replace(
            "/^'(.*)'(?:::.*)$/",
            '$1',
            $default,
        );
    }

    /**
     * Get the query to describe indexes
     *
     * @return string
     */
    private function describeIndexQuery(): string
    {
        return 'SELECT
        c2.relname,
        a.attname,
        i.indisprimary,
        i.indisunique
        FROM pg_catalog.pg_namespace n
        INNER JOIN pg_catalog.pg_class c ON (n.oid = c.relnamespace)
        INNER JOIN pg_catalog.pg_index i ON (c.oid = i.indrelid)
        INNER JOIN pg_catalog.pg_class c2 ON (c2.oid = i.indexrelid)
        INNER JOIN pg_catalog.pg_attribute a ON (a.attrelid = c.oid AND i.indrelid::regclass = a.attrelid::regclass)
        WHERE n.nspname = ?
        AND a.attnum = ANY(i.indkey)
        AND c.relname = ?
        ORDER BY i.indisprimary DESC, i.indisunique DESC, c.relname, a.attnum';
    }

    /**
     * @inheritDoc
     */
    public function describeIndexSql(string $tableName, array $config): array
    {
        $sql = $this->describeIndexQuery();
        [$schema, $name] = $this->splitTablename($tableName, $config);

        return [$sql, [$schema, $name]];
    }

    /**
     * @inheritDoc
     */
    public function convertIndexDescription(TableSchema $schema, array $row): void
    {
        $type = TableSchema::INDEX_INDEX;
        $name = $row['relname'];
        if ($row['indisprimary']) {
            $name = TableSchema::CONSTRAINT_PRIMARY;
            $type = TableSchema::CONSTRAINT_PRIMARY;
        }
        if ($row['indisunique'] && $type === TableSchema::INDEX_INDEX) {
            $type = TableSchema::CONSTRAINT_UNIQUE;
        }
        if ($type === TableSchema::CONSTRAINT_PRIMARY || $type === TableSchema::CONSTRAINT_UNIQUE) {
            $this->_convertConstraint($schema, $name, $type, $row);

            return;
        }
        $index = $schema->getIndex($name);
        if (!$index) {
            $index = [
                'type' => $type,
                'columns' => [],
            ];
        }
        $index['columns'][] = $row['attname'];
        $schema->addIndex($name, $index);
    }

    /**
     * @inheritDoc
     */
    public function describeIndexes(string $tableName): array
    {
        [$schema, $name] = $this->splitTablename($tableName);
        $sql = $this->describeIndexQuery();

        $indexes = [];
        $statement = $this->_driver->execute($sql, [$schema, $name]);
        foreach ($statement->fetchAll('assoc') as $row) {
            $type = TableSchema::INDEX_INDEX;
            $name = $row['relname'];
            $constraint = null;
            if ($row['indisprimary']) {
                $constraint = $name;
                $name = TableSchema::CONSTRAINT_PRIMARY;
                $type = TableSchema::CONSTRAINT_PRIMARY;
            }
            if ($row['indisunique'] && $type === TableSchema::INDEX_INDEX) {
                $type = TableSchema::CONSTRAINT_UNIQUE;
            }
            if (!isset($indexes[$name])) {
                $indexes[$name] = [
                    'name' => $name,
                    'type' => $type,
                    'columns' => [],
                    'length' => [],
                ];
            }
            if ($constraint) {
                $indexes[$name]['constraint'] = $constraint;
            }
            $indexes[$name]['columns'][] = $row['attname'];
        }

        return array_values($indexes);
    }

    /**
     * Add/update a constraint into the schema object.
     *
     * @param \Onepix\FoodSpotVendor\Cake\Database\Schema\TableSchema $schema The table to update.
     * @param string $name The index name.
     * @param string $type The index type.
     * @param array $row The metadata record to update with.
     * @return void
     */
    protected function _convertConstraint(TableSchema $schema, string $name, string $type, array $row): void
    {
        $constraint = $schema->getConstraint($name);
        if (!$constraint) {
            $constraint = [
                'type' => $type,
                'columns' => [],
            ];
        }
        $constraint['columns'][] = $row['attname'];
        $schema->addConstraint($name, $constraint);
    }

    /**
     * @inheritDoc
     */
    public function describeForeignKeySql(string $tableName, array $config): array
    {
        $sql = $this->describeForeignKeyQuery();
        [$schema, $name] = $this->splitTablename($tableName, $config);

        return [$sql, [$schema, $name]];
    }

    /**
     * @inheritDoc
     */
    public function convertForeignKeyDescription(TableSchema $schema, array $row): void
    {
        $data = [
            'type' => TableSchema::CONSTRAINT_FOREIGN,
            'columns' => $row['column_name'],
            'references' => [$row['references_table'], $row['references_field']],
            'update' => $this->_convertOnClause($row['on_update']),
            'delete' => $this->_convertOnClause($row['on_delete']),
        ];
        $schema->addConstraint($row['name'], $data);
    }

    /**
     * @inheritDoc
     */
    public function describeForeignKeys(string $tableName): array
    {
        [$schema, $name] = $this->splitTablename($tableName);
        $sql = $this->describeForeignKeyQuery();
        $keys = [];
        $statement = $this->_driver->execute($sql, [$schema, $name]);
        foreach ($statement->fetchAll('assoc') as $row) {
            $name = $row['name'];
            if (!isset($keys[$name])) {
                $keys[$name] = [
                    'name' => $name,
                    'type' => TableSchema::CONSTRAINT_FOREIGN,
                    'columns' => [],
                    'references' => [$row['references_table'], []],
                    'update' => $this->_convertOnClause($row['on_update']),
                    'delete' => $this->_convertOnClause($row['on_delete']),
                ];
            }
            // column indexes start at 1
            $columnOrder = $row['column_order'] - 1;
            $referencedColumnOrder = $row['references_field_order'] - 1;

            $keys[$name]['columns'][$columnOrder] = $row['column_name'];
            $keys[$name]['references'][1][$referencedColumnOrder] = $row['references_field'];
        }
        foreach ($keys as $id => $key) {
            // references.1 is the referenced columns. Backwards compat
            // requires a single column to be a string, but multiple to be an array.
            if (count($key['references'][1]) === 1) {
                $keys[$id]['references'][1] = $key['references'][1][0];
            }
        }

        return array_values($keys);
    }

    /**
     * Get the query to describe foreign keys
     *
     * @return string
     */
    private function describeForeignKeyQuery(): string
    {
        // phpcs:disable Generic.Files.LineLength
        $sql = 'SELECT
        c.conname AS name,
        c.contype AS type,
        a.attname AS column_name,
        array_position(c.conkey, a.attnum) AS column_order,
        c.confmatchtype AS match_type,
        c.confupdtype AS on_update,
        c.confdeltype AS on_delete,
        c.confrelid::regclass AS references_table,
        ab.attname AS references_field,
        array_position(c.confkey, ab.attnum) AS references_field_order
        FROM pg_catalog.pg_namespace n
        INNER JOIN pg_catalog.pg_class cl ON (n.oid = cl.relnamespace)
        INNER JOIN pg_catalog.pg_constraint c ON (n.oid = c.connamespace)
        INNER JOIN pg_catalog.pg_attribute a ON (a.attrelid = cl.oid AND c.conrelid = a.attrelid AND a.attnum = ANY(c.conkey))
        INNER JOIN pg_catalog.pg_attribute ab ON (a.attrelid = cl.oid AND c.confrelid = ab.attrelid AND ab.attnum = ANY(c.confkey))
        WHERE n.nspname = ?
        AND cl.relname = ?
        ORDER BY name, column_order ASC, references_field_order ASC';
        // phpcs:enable Generic.Files.LineLength

        return $sql;
    }

    /**
     * @inheritDoc
     */
    public function describeOptions(string $tableName): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function _convertOnClause(string $clause): string
    {
        if ($clause === 'r') {
            return TableSchema::ACTION_RESTRICT;
        }
        if ($clause === 'a') {
            return TableSchema::ACTION_NO_ACTION;
        }
        if ($clause === 'c') {
            return TableSchema::ACTION_CASCADE;
        }

        return TableSchema::ACTION_SET_NULL;
    }

    /**
     * @inheritDoc
     */
    public function columnSql(TableSchema $schema, string $name): string
    {
        $data = $schema->getColumn($name);
        assert($data !== null);
        $data['name'] = $name;

        $sql = $this->_getTypeSpecificColumnSql($data['type'], $schema, $name);
        if ($sql !== null) {
            return $sql;
        }
        $autoIncrementTypes = [
            TableSchemaInterface::TYPE_TINYINTEGER,
            TableSchemaInterface::TYPE_SMALLINTEGER,
            TableSchemaInterface::TYPE_INTEGER,
            TableSchemaInterface::TYPE_BIGINTEGER,
        ];
        $primaryKey = $schema->getPrimaryKey();
        if (
            in_array($data['type'], $autoIncrementTypes, true) &&
            $primaryKey === [$name] && $name === 'id'
        ) {
            $data['autoIncrement'] = true;
        }

        return $this->columnDefinitionSql($data);
    }

    /**
     * @inheritDoc
     */
    public function columnDefinitionSql(array $column): string
    {
        $name = $column['name'];
        $column += [
            'length' => null,
            'precision' => null,
        ];
        $out = $this->_driver->quoteIdentifier($name);
        $typeMap = [
            TableSchemaInterface::TYPE_TINYINTEGER => ' SMALLINT',
            TableSchemaInterface::TYPE_SMALLINTEGER => ' SMALLINT',
            TableSchemaInterface::TYPE_INTEGER => ' INT',
            TableSchemaInterface::TYPE_BIGINTEGER => ' BIGINT',
            TableSchemaInterface::TYPE_BINARY_UUID => ' UUID',
            TableSchemaInterface::TYPE_BOOLEAN => ' BOOLEAN',
            TableSchemaInterface::TYPE_FLOAT => ' FLOAT',
            TableSchemaInterface::TYPE_DECIMAL => ' DECIMAL',
            TableSchemaInterface::TYPE_DATE => ' DATE',
            TableSchemaInterface::TYPE_TIME => ' TIME',
            TableSchemaInterface::TYPE_DATETIME => ' TIMESTAMP',
            TableSchemaInterface::TYPE_DATETIME_FRACTIONAL => ' TIMESTAMP',
            TableSchemaInterface::TYPE_TIMESTAMP => ' TIMESTAMP',
            TableSchemaInterface::TYPE_TIMESTAMP_FRACTIONAL => ' TIMESTAMP',
            TableSchemaInterface::TYPE_TIMESTAMP_TIMEZONE => ' TIMESTAMPTZ',
            TableSchemaInterface::TYPE_UUID => ' UUID',
            TableSchemaInterface::TYPE_NATIVE_UUID => ' UUID',
            TableSchemaInterface::TYPE_CHAR => ' CHAR',
            TableSchemaInterface::TYPE_JSON => ' JSONB',
            TableSchemaInterface::TYPE_GEOMETRY => ' GEOGRAPHY(GEOMETRY, %s)',
            TableSchemaInterface::TYPE_POINT => ' GEOGRAPHY(POINT, %s)',
            TableSchemaInterface::TYPE_LINESTRING => ' GEOGRAPHY(LINESTRING, %s)',
            TableSchemaInterface::TYPE_POLYGON => ' GEOGRAPHY(POLYGON, %s)',
        ];

        $autoIncrementTypes = [
            TableSchemaInterface::TYPE_TINYINTEGER,
            TableSchemaInterface::TYPE_SMALLINTEGER,
            TableSchemaInterface::TYPE_INTEGER,
            TableSchemaInterface::TYPE_BIGINTEGER,
        ];
        $autoIncrement = (bool)($column['autoIncrement'] ?? false);
        if (
            in_array($column['type'], $autoIncrementTypes, true) &&
            $autoIncrement
        ) {
            $typeMap[$column['type']] = str_replace('INT', 'SERIAL', $typeMap[$column['type']]);
            unset($column['default']);
        }

        if (isset($typeMap[$column['type']])) {
            $out .= $typeMap[$column['type']];
        }

        if ($column['type'] === TableSchemaInterface::TYPE_TEXT && $column['length'] !== TableSchema::LENGTH_TINY) {
            $out .= ' TEXT';
        }
        if ($column['type'] === TableSchemaInterface::TYPE_BINARY) {
            $out .= ' BYTEA';
        }

        if ($column['type'] === TableSchemaInterface::TYPE_CHAR) {
            $out .= '(' . $column['length'] . ')';
        }

        if (
            $column['type'] === TableSchemaInterface::TYPE_STRING ||
            (
                $column['type'] === TableSchemaInterface::TYPE_TEXT &&
                $column['length'] === TableSchema::LENGTH_TINY
            )
        ) {
            $out .= ' VARCHAR';
            if (isset($column['length']) && $column['length'] !== '') {
                $out .= '(' . $column['length'] . ')';
            }
        }

        $hasCollate = [
            TableSchemaInterface::TYPE_TEXT,
            TableSchemaInterface::TYPE_STRING,
            TableSchemaInterface::TYPE_CHAR,
        ];
        if (in_array($column['type'], $hasCollate, true) && isset($column['collate']) && $column['collate'] !== '') {
            $out .= ' COLLATE "' . $column['collate'] . '"';
        }

        $hasPrecision = [
            TableSchemaInterface::TYPE_FLOAT,
            TableSchemaInterface::TYPE_DATETIME,
            TableSchemaInterface::TYPE_DATETIME_FRACTIONAL,
            TableSchemaInterface::TYPE_TIMESTAMP,
            TableSchemaInterface::TYPE_TIMESTAMP_FRACTIONAL,
            TableSchemaInterface::TYPE_TIMESTAMP_TIMEZONE,
        ];
        if (in_array($column['type'], $hasPrecision) && isset($column['precision'])) {
            $out .= '(' . $column['precision'] . ')';
        }

        if (
            $column['type'] === TableSchemaInterface::TYPE_DECIMAL &&
            (
                isset($column['length']) ||
                isset($column['precision'])
            )
        ) {
            $out .= '(' . $column['length'] . ',' . (int)$column['precision'] . ')';
        }
        if (in_array($column['type'], TableSchemaInterface::GEOSPATIAL_TYPES)) {
            $out = sprintf($out, $column['srid'] ?? self::DEFAULT_SRID);
        }

        if (isset($column['null']) && $column['null'] === false) {
            $out .= ' NOT NULL';
        }

        $datetimeTypes = [
            TableSchemaInterface::TYPE_DATETIME,
            TableSchemaInterface::TYPE_DATETIME_FRACTIONAL,
            TableSchemaInterface::TYPE_TIMESTAMP,
            TableSchemaInterface::TYPE_TIMESTAMP_FRACTIONAL,
            TableSchemaInterface::TYPE_TIMESTAMP_TIMEZONE,
        ];
        if (
            isset($column['default']) &&
            in_array($column['type'], $datetimeTypes) &&
            strtolower($column['default']) === 'current_timestamp'
        ) {
            $out .= ' DEFAULT CURRENT_TIMESTAMP';
        } elseif (isset($column['default'])) {
            $defaultValue = $column['default'];
            if ($column['type'] === 'boolean') {
                $defaultValue = (bool)$defaultValue;
            }
            $out .= ' DEFAULT ' . $this->_driver->schemaValue($defaultValue);
        } elseif (isset($column['null']) && $column['null'] !== false) {
            $out .= ' DEFAULT NULL';
        }

        return $out;
    }

    /**
     * @inheritDoc
     */
    public function addConstraintSql(TableSchema $schema): array
    {
        $sqlPattern = 'ALTER TABLE %s ADD %s;';
        $sql = [];

        foreach ($schema->constraints() as $name) {
            $constraint = $schema->getConstraint($name);
            assert($constraint !== null);
            if ($constraint['type'] === TableSchema::CONSTRAINT_FOREIGN) {
                $tableName = $this->_driver->quoteIdentifier($schema->name());
                $sql[] = sprintf($sqlPattern, $tableName, $this->constraintSql($schema, $name));
            }
        }

        return $sql;
    }

    /**
     * @inheritDoc
     */
    public function dropConstraintSql(TableSchema $schema): array
    {
        $sqlPattern = 'ALTER TABLE %s DROP CONSTRAINT %s;';
        $sql = [];

        foreach ($schema->constraints() as $name) {
            $constraint = $schema->getConstraint($name);
            assert($constraint !== null);
            if ($constraint['type'] === TableSchema::CONSTRAINT_FOREIGN) {
                $tableName = $this->_driver->quoteIdentifier($schema->name());
                $constraintName = $this->_driver->quoteIdentifier($name);
                $sql[] = sprintf($sqlPattern, $tableName, $constraintName);
            }
        }

        return $sql;
    }

    /**
     * @inheritDoc
     */
    public function indexSql(TableSchema $schema, string $name): string
    {
        $data = $schema->getIndex($name);
        assert($data !== null);
        $columns = array_map(
            $this->_driver->quoteIdentifier(...),
            $data['columns'],
        );

        return sprintf(
            'CREATE INDEX %s ON %s (%s)',
            $this->_driver->quoteIdentifier($name),
            $this->_driver->quoteIdentifier($schema->name()),
            implode(', ', $columns),
        );
    }

    /**
     * @inheritDoc
     */
    public function constraintSql(TableSchema $schema, string $name): string
    {
        $data = $schema->getConstraint($name);
        assert($data !== null);
        $out = 'CONSTRAINT ' . $this->_driver->quoteIdentifier($name);
        if ($data['type'] === TableSchema::CONSTRAINT_PRIMARY) {
            $out = 'PRIMARY KEY';
        }
        if ($data['type'] === TableSchema::CONSTRAINT_UNIQUE) {
            $out .= ' UNIQUE';
        }

        return $this->_keySql($out, $data);
    }

    /**
     * Helper method for generating key SQL snippets.
     *
     * @param string $prefix The key prefix
     * @param array<string, mixed> $data Key data.
     * @return string
     */
    protected function _keySql(string $prefix, array $data): string
    {
        $columns = array_map(
            $this->_driver->quoteIdentifier(...),
            $data['columns'],
        );
        if ($data['type'] === TableSchema::CONSTRAINT_FOREIGN) {
            return $prefix . sprintf(
                ' FOREIGN KEY (%s) REFERENCES %s (%s) ON UPDATE %s ON DELETE %s DEFERRABLE INITIALLY IMMEDIATE',
                implode(', ', $columns),
                $this->_driver->quoteIdentifier($data['references'][0]),
                $this->_convertConstraintColumns($data['references'][1]),
                $this->_foreignOnClause($data['update']),
                $this->_foreignOnClause($data['delete']),
            );
        }

        return $prefix . ' (' . implode(', ', $columns) . ')';
    }

    /**
     * @inheritDoc
     */
    public function createTableSql(TableSchema $schema, array $columns, array $constraints, array $indexes): array
    {
        $content = array_merge($columns, $constraints);
        $content = implode(",\n", array_filter($content));
        $tableName = $this->_driver->quoteIdentifier($schema->name());
        $dbSchema = $this->_driver->schema();
        if ($dbSchema !== 'public') {
            $tableName = $this->_driver->quoteIdentifier($dbSchema) . '.' . $tableName;
        }
        $temporary = $schema->isTemporary() ? ' TEMPORARY ' : ' ';
        $out = [];
        $out[] = sprintf("CREATE%sTABLE %s (\n%s\n)", $temporary, $tableName, $content);
        foreach ($indexes as $index) {
            $out[] = $index;
        }
        foreach ($schema->columns() as $column) {
            $columnData = $schema->getColumn($column);
            if (isset($columnData['comment'])) {
                $out[] = sprintf(
                    'COMMENT ON COLUMN %s.%s IS %s',
                    $tableName,
                    $this->_driver->quoteIdentifier($column),
                    $this->_driver->schemaValue($columnData['comment']),
                );
            }
        }

        return $out;
    }

    /**
     * @inheritDoc
     */
    public function truncateTableSql(TableSchema $schema): array
    {
        $name = $this->_driver->quoteIdentifier($schema->name());

        return [
            sprintf('TRUNCATE %s RESTART IDENTITY CASCADE', $name),
        ];
    }

    /**
     * Generate the SQL to drop a table.
     *
     * @param \Onepix\FoodSpotVendor\Cake\Database\Schema\TableSchema $schema Table instance
     * @return array SQL statements to drop a table.
     */
    public function dropTableSql(TableSchema $schema): array
    {
        $sql = sprintf(
            'DROP TABLE %s CASCADE',
            $this->_driver->quoteIdentifier($schema->name()),
        );

        return [$sql];
    }
}
