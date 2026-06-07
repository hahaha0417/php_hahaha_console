<?php

namespace App\Console\Commands\tool;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;

class hahaha_command_laravel_table_clear extends Command
{
    /**
     * Table group key `1` means clear all configured tables.
     *
     * Example:
     * - `2` clears the application tables in this group.
     * - `3` clears another batch of application tables.
     *
     * @var array<int, array<int, string>>
     */
    public array $table_groups_ = [
        2 => [
            // 'hahaha_users',
            // 'hahaha_orders',
            // 'hahaha_order_items',
        ],
        3 => [
            // 'hahaha_sessions',
            // 'hahaha_password_reset_tokens',
        ],
    ];

    /**
     * Example:
     * php artisan tool:laravel:table:clear --database="mariadb"
     *
     * php artisan tool:laravel:table:clear --database="mariadb
     * redis"
     *
     * php artisan tool:laravel:table:clear --database="mariadb
     * mysql" --all=1
     */
    protected $signature = 'tool:laravel:table:clear
        {--database= : Copied database or redis connection names separated by new lines}
        {--all=2 : Table group key to clear, 1 clears all configured tables}';

    protected $description = 'Clear all records from configured table groups on selected database connections and flush selected redis connections';

    public function handle(): int
    {
        $database_connections_ = $this->list_items_resolve_((string) $this->option('database'));
        $table_names_ = $this->table_names_resolve_((int) $this->option('all'));
        $clear_targets_ = $this->clear_targets_resolve_($database_connections_);

        if ($table_names_ === null || $clear_targets_ === null) {
            return self::FAILURE;
        }

        foreach ($clear_targets_ as $clear_target_) {
            if ($clear_target_['mode'] === 'redis_connection') {
                $is_flushed_ = $this->redis_connection_flush_($clear_target_['name']);

                if (! $is_flushed_) {
                    return self::FAILURE;
                }

                $this->components->info('Flushed '.$this->clear_target_label_resolve_($clear_target_).'.');

                continue;
            }

            $deleted_records_count_ = $this->database_tables_clear_($table_names_, $clear_target_['name']);

            if ($deleted_records_count_ === null) {
                return self::FAILURE;
            }

            $this->components->info(
                'Deleted '
                .$deleted_records_count_
                .' records from '
                .$this->clear_target_label_resolve_($clear_target_)
                .'.'
            );
        }

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>|null
     */
    private function table_names_resolve_(int $table_group_key_): ?array
    {
        if ($table_group_key_ === 1) {
            $table_names_ = [];

            foreach ($this->table_groups_ as $table_group_) {
                foreach ($table_group_ as $table_name_) {
                    $resolved_table_name_ = trim((string) $table_name_);

                    if ($resolved_table_name_ === '') {
                        continue;
                    }

                    $table_names_[] = $resolved_table_name_;
                }
            }

            $table_names_ = array_values(array_unique($table_names_));

            if ($table_names_ === []) {
                $this->components->error('No table names were configured for the clear-all operation.');

                return null;
            }

            return $table_names_;
        }

        if (! array_key_exists($table_group_key_, $this->table_groups_)) {
            $this->components->error('Unsupported table group key: '.$table_group_key_);

            return null;
        }

        $table_names_ = [];

        foreach ($this->table_groups_[$table_group_key_] as $table_name_) {
            $resolved_table_name_ = trim((string) $table_name_);

            if ($resolved_table_name_ === '') {
                continue;
            }

            $table_names_[] = $resolved_table_name_;
        }

        $table_names_ = array_values(array_unique($table_names_));

        if ($table_names_ === []) {
            $this->components->error('No table names were configured for table group key: '.$table_group_key_);

            return null;
        }

        return $table_names_;
    }

    /**
     * @param  array<int, string>  $database_connections_
     * @return array<int, array{mode: string, name: string}>|null
     */
    private function clear_targets_resolve_(array $database_connections_): ?array
    {
        if ($database_connections_ === []) {
            return [[
                'mode' => 'database_connection',
                'name' => (string) config('database.default'),
            ]];
        }

        $clear_targets_ = [];

        foreach ($database_connections_ as $database_connection_) {
            if (is_array(config('database.connections.'.$database_connection_))) {
                $clear_targets_[] = [
                    'mode' => 'database_connection',
                    'name' => $database_connection_,
                ];

                continue;
            }

            if (is_array(config('database.redis.'.$database_connection_))) {
                $clear_targets_[] = [
                    'mode' => 'redis_connection',
                    'name' => $database_connection_,
                ];

                continue;
            }

            $this->components->error('Unsupported database or redis connection: '.$database_connection_);

            return null;
        }

        return $clear_targets_;
    }

    /**
     * @param  array<int, string>  $table_names_
     */
    private function database_tables_clear_(array $table_names_, string $database_connection_): ?int
    {
        foreach ($table_names_ as $table_name_) {
            if (! Schema::connection($database_connection_)->hasTable($table_name_)) {
                $this->components->error(
                    'Missing table ['.$table_name_.'] on database connection ['.$database_connection_.'].'
                );

                return null;
            }
        }

        $deleted_records_count_ = 0;

        Schema::connection($database_connection_)->disableForeignKeyConstraints();

        try {
            foreach ($table_names_ as $table_name_) {
                $deleted_records_count_ += DB::connection($database_connection_)
                    ->table($table_name_)
                    ->delete();
            }
        } finally {
            Schema::connection($database_connection_)->enableForeignKeyConstraints();
        }

        return $deleted_records_count_;
    }

    private function redis_connection_flush_(string $redis_connection_): bool
    {
        return (bool) Redis::connection($redis_connection_)->flushdb();
    }

    /**
     * @param  array{mode: string, name: string}  $clear_target_
     */
    private function clear_target_label_resolve_(array $clear_target_): string
    {
        if ($clear_target_['mode'] === 'redis_connection') {
            return 'redis connection ['.$clear_target_['name'].']';
        }

        return 'database connection ['.$clear_target_['name'].']';
    }

    /**
     * @return array<int, string>
     */
    private function list_items_resolve_(string $list_input_): array
    {
        $trimmed_input_ = trim($list_input_);

        if ($trimmed_input_ === '') {
            return [];
        }

        $list_entries_ = preg_split('/\r\n|\r|\n/', $trimmed_input_) ?: [];
        $resolved_entries_ = [];

        foreach ($list_entries_ as $list_entry_) {
            $resolved_entry_ = trim((string) $list_entry_);

            if ($resolved_entry_ === '') {
                continue;
            }

            $resolved_entries_[] = $resolved_entry_;
        }

        return $resolved_entries_;
    }
}
