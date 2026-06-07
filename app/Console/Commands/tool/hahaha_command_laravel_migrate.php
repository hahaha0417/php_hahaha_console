<?php

namespace App\Console\Commands\tool;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class hahaha_command_laravel_migrate extends Command
{
    /**
     * Supported commands: migrate, migrate:refresh, migrate:rollback,
     * migrations:log, migrations:delete
     */
    public string $migrate_command_ = 'migrate';

    /**
     * Accepts either migration files or migration directories.
     *
     * @var array<int, string>
     */
    public array $migrate_paths_ = [
        // 'database/migrations/2026_06_07_000001_create_demo_table.php',
        // 'database/migrations/demo',
    ];

    protected $signature = 'tool:laravel:migrate
        {--database= : Copied database connection names separated by new lines}';

    protected $description = 'Run Laravel migrations for configured migration files or directories and selected database connections';

    public function handle(): int
    {
        $migrate_command_ = $this->migrate_command_resolve_();
        $migrate_paths_ = $this->migrate_paths_resolve_();
        $database_connections_ = $this->list_items_resolve_((string) $this->option('database'));
        $migrate_jobs_ = [];

        if ($database_connections_ === []) {
            $migrate_jobs_[] = [
                'db' => null,
                'migrate' => $migrate_paths_,
            ];
        } else {
            foreach ($database_connections_ as $database_connection_) {
                $migrate_jobs_[] = [
                    'db' => $database_connection_,
                    'migrate' => $migrate_paths_,
                ];
            }
        }

        foreach ($migrate_jobs_ as $migrate_job_) {
            if (in_array($migrate_command_, ['migrations:log', 'migrations:delete'], true)) {
                $exit_code_ = $this->migrations_repository_update_(
                    $migrate_command_,
                    $migrate_job_['migrate'],
                    is_string($migrate_job_['db']) ? $migrate_job_['db'] : null
                );

                if ($exit_code_ !== self::SUCCESS) {
                    return $exit_code_;
                }

                continue;
            }

            $migrate_command_options_ = [];

            if ($migrate_job_['migrate'] !== []) {
                $migrate_command_options_['--path'] = $migrate_job_['migrate'];
            }

            if ($this->list_items_include_absolute_path_($migrate_job_['migrate'])) {
                $migrate_command_options_['--realpath'] = true;
            }

            if (is_string($migrate_job_['db']) && $migrate_job_['db'] !== '') {
                $migrate_command_options_['--database'] = $migrate_job_['db'];
            }

            $exit_code_ = $this->call($migrate_command_, $migrate_command_options_);

            if ($exit_code_ !== self::SUCCESS) {
                return $exit_code_;
            }
        }

        return self::SUCCESS;
    }

    private function migrate_command_resolve_(): string
    {
        $migrate_command_ = trim($this->migrate_command_);

        if (! in_array($migrate_command_, [
            'migrate',
            'migrate:refresh',
            'migrate:rollback',
            'migrations:log',
            'migrations:delete',
        ], true)) {
            $this->components->error('Unsupported migrate command: '.$migrate_command_);

            return 'migrate';
        }

        return $migrate_command_;
    }

    /**
     * @return array<int, string>
     */
    private function migrate_paths_resolve_(): array
    {
        $migrate_paths_ = [];

        foreach ($this->migrate_paths_ as $migrate_path_) {
            $resolved_migrate_path_ = trim((string) $migrate_path_);

            if ($resolved_migrate_path_ === '') {
                continue;
            }

            $migrate_paths_[] = str_replace('\\', '/', $resolved_migrate_path_);
        }

        return array_values(array_unique($migrate_paths_));
    }

    /**
     * @param  array<int, string>  $migrate_paths_
     */
    private function migrations_repository_update_(
        string $migrate_command_,
        array $migrate_paths_,
        ?string $database_connection_
    ): int {
        $migration_names_ = $this->migration_names_resolve_($migrate_paths_);
        $migrator_ = app('migrator');
        $repository_ = $migrator_->getRepository();

        if ($database_connection_ !== null && $database_connection_ !== '') {
            $repository_->setSource($database_connection_);
        }

        if (! $repository_->repositoryExists()) {
            $repository_->createRepository();
        }

        $ran_migrations_ = $repository_->getRan();
        $next_batch_number_ = $repository_->getNextBatchNumber();

        foreach ($migration_names_ as $migration_name_) {
            if ($migrate_command_ === 'migrations:log') {
                if (in_array($migration_name_, $ran_migrations_, true)) {
                    continue;
                }

                $repository_->log($migration_name_, $next_batch_number_);

                continue;
            }

            if (! in_array($migration_name_, $ran_migrations_, true)) {
                continue;
            }

            $repository_->delete((object) ['migration' => $migration_name_]);
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<int, string>  $migrate_paths_
     * @return array<int, string>
     */
    private function migration_names_resolve_(array $migrate_paths_): array
    {
        $migration_names_ = [];

        foreach ($migrate_paths_ as $migrate_path_) {
            $resolved_migrate_path_ = $this->migrate_path_absolute_resolve_($migrate_path_);

            if (File::isFile($resolved_migrate_path_)) {
                $migration_names_[] = pathinfo($resolved_migrate_path_, PATHINFO_FILENAME);

                continue;
            }

            if (! File::isDirectory($resolved_migrate_path_)) {
                continue;
            }

            foreach (File::files($resolved_migrate_path_) as $migration_file_) {
                $migration_names_[] = pathinfo($migration_file_->getFilename(), PATHINFO_FILENAME);
            }
        }

        return array_values(array_unique($migration_names_));
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

    /**
     * @param  array<int, string>  $paths_
     */
    private function list_items_include_absolute_path_(array $paths_): bool
    {
        foreach ($paths_ as $path_) {
            if ($this->path_is_absolute_($path_)) {
                return true;
            }
        }

        return false;
    }

    private function path_is_absolute_(string $path_input_): bool
    {
        if ($path_input_ === '') {
            return false;
        }

        if (preg_match('/^[A-Za-z]:[\\\\\\/]/', $path_input_) === 1) {
            return true;
        }

        return str_starts_with($path_input_, '/')
            || str_starts_with($path_input_, '\\');
    }

    private function migrate_path_absolute_resolve_(string $migrate_path_): string
    {
        if ($this->path_is_absolute_($migrate_path_)) {
            return $migrate_path_;
        }

        return base_path(str_replace('/', DIRECTORY_SEPARATOR, $migrate_path_));
    }
}
