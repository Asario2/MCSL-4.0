<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDatabaseCollation extends Command
{
    protected $signature = 'db:fix-collation';

    protected $description = 'Convert complete database to utf8mb4_unicode_ci';

    protected string $charset = 'utf8mb4';
    protected string $collation = 'utf8mb4_unicode_ci';

    public function handle()
    {
        $connection = DB::connection('mariadb');

        $db = $connection->getDatabaseName();

        $this->info("Database: {$db}");

        try {

            $connection->statement("
                ALTER DATABASE `{$db}`
                CHARACTER SET {$this->charset}
                COLLATE {$this->collation}
            ");

            $this->info("✓ Database default updated");

        } catch (\Throwable $e) {

            $this->error("Database failed:");
            $this->error($e->getMessage());
        }

        $tables = $connection->select('SHOW TABLES');

        $key = 'Tables_in_' . $db;

        foreach ($tables as $tableObj) {

            $table = $tableObj->$key;

            $this->newLine();
            $this->info("Table: {$table}");

            try {

                $connection->statement("
                    ALTER TABLE `{$table}`
                    CONVERT TO CHARACTER SET {$this->charset}
                    COLLATE {$this->collation}
                ");

                $this->line("  ✓ Table converted");

            } catch (\Throwable $e) {

                $this->warn("  ✗ Table convert failed");
                $this->warn("  {$e->getMessage()}");

                continue;
            }

            $columns = $connection->select("
                SHOW FULL COLUMNS
                FROM `{$table}`
            ");

            foreach ($columns as $column) {

                $type = strtolower($column->Type);

                if (
                    !str_contains($type, 'varchar')
                    && !str_contains($type, 'text')
                    && !str_contains($type, 'char')
                ) {
                    continue;
                }

                $field = $column->Field;

                try {

                    $null = $column->Null === 'YES'
                        ? 'NULL'
                        : 'NOT NULL';

                    $default = '';

                    if ($column->Default !== null) {

                        $default =
                            'DEFAULT ' .
                            $connection->getPdo()->quote($column->Default);
                    }

                    $extra = $column->Extra ?? '';

                    $sql = "
                        ALTER TABLE `{$table}`
                        MODIFY `{$field}`
                        {$column->Type}
                        CHARACTER SET {$this->charset}
                        COLLATE {$this->collation}
                        {$null}
                        {$default}
                        {$extra}
                    ";

                    $connection->statement($sql);

                    $this->line("    ✓ {$field}");

                } catch (\Throwable $e) {

                    $this->warn("    ✗ {$field}");
                    $this->warn("      {$e->getMessage()}");
                }
            }
        }

        $this->newLine();
        $this->info("==================================");
        $this->info("Finished");
        $this->info("Connection: mariadb");
        $this->info("Charset   : {$this->charset}");
        $this->info("Collation : {$this->collation}");
        $this->info("==================================");

        return self::SUCCESS;
    }
}
