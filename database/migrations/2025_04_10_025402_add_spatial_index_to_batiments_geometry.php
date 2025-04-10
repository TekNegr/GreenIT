<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // For MySQL/MariaDB
        if (in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'])) {
            // First update all null geometries to a default point
            DB::statement("UPDATE batiments SET geometry = POINT(0,0) WHERE geometry IS NULL");
            // Then modify column to NOT NULL
            DB::statement('ALTER TABLE batiments MODIFY geometry POINT NOT NULL');
            // Finally create the spatial index
            DB::statement('CREATE SPATIAL INDEX spatial_index ON batiments(geometry)');
        }
        // For PostgreSQL
        elseif (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX spatial_index ON batiments USING GIST(geometry)');
        }
    }

    public function down()
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('DROP INDEX spatial_index ON batiments');
        }
        elseif (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS spatial_index');
        }
    }
};
