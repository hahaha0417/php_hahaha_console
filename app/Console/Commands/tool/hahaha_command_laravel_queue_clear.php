<?php

namespace App\Console\Commands\tool;

use App\Enums\db\hahaha as HahahaTable;
use App\Enums\db\hahaha\jobs as HahahaJobsColumn;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ClearableQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ReflectionClass;

class hahaha_command_laravel_queue_clear extends Command
{
    /**
     * Queue group key `1` means clear all configured queues.
     *
     * @var array<int, array<int, string>>
     */
    public array $queue_groups_ = [
        2 => [
            // 'default',
        ],
    ];

    /**
     * Usage examples:
     * php artisan tool:laravel:queue:clear --connection=database --database=mysql
     * php artisan tool:laravel:queue:clear --connection=redis --database=0
     * php artisan tool:laravel:queue:clear --connection=redis --database=default
     * php artisan tool:laravel:queue:clear --connection=database --database="mysql\nsqlite" --all=1
     */
    protected $signature = 'tool:laravel:queue:clear
        {--database= : Copied database connection names or redis database numbers separated by new lines}
        {--connection= : Copied queue connection types separated by new lines. Supported: database, redis}
        {--all=2 : Queue group key to clear, 1 clears all configured queues}';

    protected $description = 'Clear queued jobs from configured queue groups on selected database or redis targets';

    public function handle(): int
    {
        $database_targets_ = $this->list_items_resolve_((string) $this->option('database'));
        $connection_types_ = $this->connection_types_resolve_((string) $this->option('connection'));
        $queue_group_key_ = (int) $this->option('all');
        $queue_names_ = $this->queue_names_resolve_($queue_group_key_);
        $clear_targets_ = $this->clear_targets_resolve_($connection_types_, $database_targets_);

        if ($queue_names_ === null || $connection_types_ === null || $clear_targets_ === null) {
            return self::FAILURE;
        }

        foreach ($clear_targets_ as $clear_target_) {
            $deleted_jobs_count_ = $this->queue_jobs_clear_($queue_names_, $clear_target_);

            if ($deleted_jobs_count_ === null) {
                return self::FAILURE;
            }

            $this->components->info(
                'Deleted '
                .$deleted_jobs_count_
                .' queue jobs from '
                .$this->clear_target_label_resolve_($clear_target_)
                .'.'
            );
        }

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>|null
     */
    private function queue_names_resolve_(int $queue_group_key_): ?array
    {
        if ($queue_group_key_ === 1) {
            $queue_names_ = [];

            foreach ($this->queue_groups_ as $queue_group_) {
                foreach ($queue_group_ as $queue_name_) {
                    $resolved_queue_name_ = trim((string) $queue_name_);

                    if ($resolved_queue_name_ === '') {
                        continue;
                    }

                    $queue_names_[] = $resolved_queue_name_;
                }
            }

            $queue_names_ = array_values(array_unique($queue_names_));

            if ($queue_names_ === []) {
                $this->components->error('No queue names were configured for the clear-all operation.');

                return null;
            }

            return $queue_names_;
        }

        if (! array_key_exists($queue_group_key_, $this->queue_groups_)) {
            $this->components->error('Unsupported queue group key: '.$queue_group_key_);

            return null;
        }

        $queue_names_ = [];

        foreach ($this->queue_groups_[$queue_group_key_] as $queue_name_) {
            $resolved_queue_name_ = trim((string) $queue_name_);

            if ($resolved_queue_name_ === '') {
                continue;
            }

            $queue_names_[] = $resolved_queue_name_;
        }

        $queue_names_ = array_values(array_unique($queue_names_));

        if ($queue_names_ === []) {
            $this->components->error('No queue names were configured for queue group key: '.$queue_group_key_);

            return null;
        }

        return $queue_names_;
    }

    /**
     * @return array<int, string>|null
     */
    private function connection_types_resolve_(string $connection_input_): ?array
    {
        $connection_types_ = $this->list_items_resolve_($connection_input_);

        if ($connection_types_ === []) {
            return [(string) config('queue.default')];
        }

        $resolved_connection_types_ = [];

        foreach ($connection_types_ as $connection_type_) {
            $resolved_connection_type_ = strtolower(trim($connection_type_));

            if (! in_array($resolved_connection_type_, ['database', 'redis'], true)) {
                $this->components->error('Unsupported queue connection type: '.$connection_type_);

                return null;
            }

            $resolved_connection_types_[] = $resolved_connection_type_;
        }

        return array_values(array_unique($resolved_connection_types_));
    }

    /**
     * @param  array<int, string>  $connection_types_
     * @param  array<int, string>  $database_targets_
     * @return array<int, array{mode: string, name: string}>|null
     */
    private function clear_targets_resolve_(array $connection_types_, array $database_targets_): ?array
    {
        $clear_targets_ = [];

        foreach ($connection_types_ as $connection_type_) {
            if ($connection_type_ === 'database') {
                $resolved_database_targets_ = $database_targets_ === []
                    ? [(string) config('queue.connections.database.connection', config('database.default'))]
                    : $database_targets_;

                foreach ($resolved_database_targets_ as $database_target_) {
                    if (! is_array(config('database.connections.'.$database_target_))) {
                        $this->components->error('Unsupported database connection: '.$database_target_);

                        return null;
                    }

                    $clear_targets_[] = [
                        'mode' => 'database_connection',
                        'name' => $database_target_,
                    ];
                }

                continue;
            }

            $resolved_redis_targets_ = $database_targets_ === [] ? ['default'] : $database_targets_;

            foreach ($resolved_redis_targets_ as $redis_target_) {
                if (preg_match('/^\d+$/', $redis_target_) === 1) {
                    $clear_targets_[] = [
                        'mode' => 'redis_database_number',
                        'name' => $redis_target_,
                    ];

                    continue;
                }

                if (! is_array(config('database.redis.'.$redis_target_))) {
                    $this->components->error('Unsupported redis connection or database number: '.$redis_target_);

                    return null;
                }

                $clear_targets_[] = [
                    'mode' => 'redis_connection',
                    'name' => $redis_target_,
                ];
            }
        }

        return $clear_targets_;
    }

    /**
     * @param  array<int, string>  $queue_names_
     * @param  array{mode: string, name: string}  $clear_target_
     */
    private function queue_jobs_clear_(array $queue_names_, array $clear_target_): ?int
    {
        if ($clear_target_['mode'] === 'database_connection') {
            return $this->database_queue_jobs_clear_($queue_names_, $clear_target_['name']);
        }

        if ($clear_target_['mode'] === 'redis_connection') {
            return $this->redis_queue_jobs_clear_($queue_names_, $clear_target_['name']);
        }

        return $this->redis_queue_jobs_clear_by_database_number_($queue_names_, $clear_target_['name']);
    }

    /**
     * @param  array<int, string>  $queue_names_
     */
    private function database_queue_jobs_clear_(array $queue_names_, ?string $database_connection_): int
    {
        $jobs_query_ = DB::connection($database_connection_)
            ->table(HahahaTable::JOBS->value)
            ->whereIn(HahahaJobsColumn::QUEUE->value, $queue_names_);

        return $jobs_query_->delete();
    }

    /**
     * @param  array<int, string>  $queue_names_
     */
    private function redis_queue_jobs_clear_(array $queue_names_, string $redis_connection_name_): ?int
    {
        return $this->queue_connection_jobs_clear_with_config_([
            ...config('queue.connections.redis', []),
            'connection' => $redis_connection_name_,
        ], 'redis['.$redis_connection_name_.']', $queue_names_);
    }

    /**
     * @param  array<int, string>  $queue_names_
     */
    private function redis_queue_jobs_clear_by_database_number_(array $queue_names_, string $redis_database_number_): ?int
    {
        $base_redis_connection_name_ = (string) config('queue.connections.redis.connection', 'default');
        $base_redis_connection_config_ = config('database.redis.'.$base_redis_connection_name_);

        if (! is_array($base_redis_connection_config_)) {
            $this->components->error('Unsupported redis base connection: '.$base_redis_connection_name_);

            return null;
        }

        $temporary_redis_connection_name_ = 'hahaha_queue_clear_redis_'.Str::random(12);
        $temporary_queue_connection_label_ = 'redis[db:'.$redis_database_number_.']';

        config([
            'database.redis.'.$temporary_redis_connection_name_ => [
                ...$base_redis_connection_config_,
                'database' => (string) $redis_database_number_,
            ],
        ]);

        return $this->queue_connection_jobs_clear_with_config_([
            ...config('queue.connections.redis', []),
            'connection' => $temporary_redis_connection_name_,
        ], $temporary_queue_connection_label_, $queue_names_);
    }

    /**
     * @param  array<string, mixed>  $queue_connection_config_
     * @param  array<int, string>  $queue_names_
     */
    private function queue_connection_jobs_clear_with_config_(
        array $queue_connection_config_,
        string $queue_connection_label_,
        array $queue_names_
    ): ?int {
        $temporary_queue_connection_name_ = 'hahaha_queue_clear_queue_'.Str::random(12);

        config([
            'queue.connections.'.$temporary_queue_connection_name_ => $queue_connection_config_,
        ]);

        $queue_connection_ = app('queue')->connection($temporary_queue_connection_name_);

        if (! $queue_connection_ instanceof ClearableQueue) {
            $this->components->error(
                'Clearing queues is not supported on ['.(new ReflectionClass($queue_connection_))->getShortName().'].'
            );

            return null;
        }

        $deleted_jobs_count_ = 0;

        foreach ($queue_names_ as $queue_name_) {
            $deleted_jobs_count_ += (int) $queue_connection_->clear($queue_name_);
        }

        return $deleted_jobs_count_;
    }

    /**
     * @param  array<int, string>  $queue_names_
     */
    private function database_queue_connection_jobs_clear_(
        array $queue_names_,
        ?string $database_connection_name_,
        string $jobs_table_name_
    ): int {
        return DB::connection($database_connection_name_)
            ->table($jobs_table_name_)
            ->whereIn(HahahaJobsColumn::QUEUE->value, $queue_names_)
            ->delete();
    }

    /**
     * @param  array{mode: string, name: string}  $clear_target_
     */
    private function clear_target_label_resolve_(array $clear_target_): string
    {
        if ($clear_target_['mode'] === 'database_connection') {
            return 'database connection ['.$clear_target_['name'].']';
        }

        if ($clear_target_['mode'] === 'redis_connection') {
            return 'redis connection ['.$clear_target_['name'].']';
        }

        return 'redis database ['.$clear_target_['name'].']';
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
