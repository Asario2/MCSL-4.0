<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MultiDbModel extends Model
{
    public function getConnectionName()
    {
        $sd = SD();

        if ($sd && config("database.connections.mariadb_$sd")) {
            return "mariadb_$sd";
        }

        return config('database.default');
    }
}
