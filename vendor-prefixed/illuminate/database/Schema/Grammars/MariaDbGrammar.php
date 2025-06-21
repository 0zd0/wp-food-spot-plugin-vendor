<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database\Schema\Grammars;

use Onepix\FoodSpotVendor\Illuminate\Database\Connection;
use Onepix\FoodSpotVendor\Illuminate\Database\Schema\Blueprint;
use Onepix\FoodSpotVendor\Illuminate\Support\Fluent;

class MariaDbGrammar extends MySqlGrammar
{
    /**
     * Compile a rename column command.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Onepix\FoodSpotVendor\Illuminate\Support\Fluent  $command
     * @param  \Onepix\FoodSpotVendor\Illuminate\Database\Connection  $connection
     * @return array|string
     */
    public function compileRenameColumn(Blueprint $blueprint, Fluent $command, Connection $connection)
    {
        if (version_compare($connection->getServerVersion(), '10.5.2', '<')) {
            return $this->compileLegacyRenameColumn($blueprint, $command, $connection);
        }

        return parent::compileRenameColumn($blueprint, $command, $connection);
    }

    /**
     * Create the column definition for a uuid type.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeUuid(Fluent $column)
    {
        return 'uuid';
    }

    /**
     * Create the column definition for a spatial Geometry type.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeGeometry(Fluent $column)
    {
        $subtype = $column->subtype ? strtolower($column->subtype) : null;

        if (! in_array($subtype, ['point', 'linestring', 'polygon', 'geometrycollection', 'multipoint', 'multilinestring', 'multipolygon'])) {
            $subtype = null;
        }

        return sprintf('%s%s',
            $subtype ?? 'geometry',
            $column->srid ? ' ref_system_id='.$column->srid : ''
        );
    }
}
