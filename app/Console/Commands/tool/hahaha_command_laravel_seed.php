<?php

namespace App\Console\Commands\tool;

use Illuminate\Console\Command;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class hahaha_command_laravel_seed extends Command
{
    /**
     * Accepts either seeder files or seeder directories.
     *
     * @var array<int, string>
     */
    public array $seeder_paths_ = [
        // 'database/seeders/HahahaUserSeeder.php',
        // 'database/seeders/demo',
    ];

    protected $signature = 'tool:laravel:seed
        {--database= : Copied database connection names separated by new lines}';

    protected $description = 'Run Laravel seeders for configured seeder files or directories and selected database connections';

    public function handle(): int
    {
        $database_connections_ = $this->list_items_resolve_((string) $this->option('database'));
        $seeder_classes_ = $this->seeder_classes_resolve_();

        if ($seeder_classes_ === null) {
            return self::FAILURE;
        }

        if ($seeder_classes_ === []) {
            $this->components->error('No seeder files were resolved from the configured seeder paths.');

            return self::FAILURE;
        }

        if ($database_connections_ === []) {
            $exit_code_ = $this->seeders_run_($seeder_classes_, null);

            return $exit_code_ === self::SUCCESS ? self::SUCCESS : self::FAILURE;
        }

        foreach ($database_connections_ as $database_connection_) {
            $exit_code_ = $this->seeders_run_($seeder_classes_, $database_connection_);

            if ($exit_code_ !== self::SUCCESS) {
                return $exit_code_;
            }
        }

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>|null
     */
    private function seeder_classes_resolve_(): ?array
    {
        $seeder_files_ = $this->seeder_files_resolve_();
        $seeder_classes_ = [];

        foreach ($seeder_files_ as $seeder_file_) {
            $seeder_class_ = $this->seeder_class_name_resolve_($seeder_file_);

            if ($seeder_class_ === null) {
                $this->components->error('Unable to resolve seeder class from file: '.$seeder_file_);

                return null;
            }

            require_once $seeder_file_;

            if (! is_subclass_of($seeder_class_, Seeder::class)) {
                $this->components->error('Resolved class is not a Laravel seeder: '.$seeder_class_);

                return null;
            }

            $seeder_classes_[] = $seeder_class_;
        }

        return array_values(array_unique($seeder_classes_));
    }

    /**
     * @return array<int, string>
     */
    private function seeder_files_resolve_(): array
    {
        $resolved_seeder_files_ = [];

        foreach ($this->seeder_paths_ as $seeder_path_) {
            $resolved_seeder_path_ = trim((string) $seeder_path_);

            if ($resolved_seeder_path_ === '') {
                continue;
            }

            $absolute_seeder_path_ = $this->path_absolute_resolve_($resolved_seeder_path_);

            if (File::isFile($absolute_seeder_path_)) {
                $resolved_seeder_files_[] = $absolute_seeder_path_;

                continue;
            }

            if (! File::isDirectory($absolute_seeder_path_)) {
                continue;
            }

            foreach (File::allFiles($absolute_seeder_path_) as $seeder_file_) {
                if ($seeder_file_->getExtension() !== 'php') {
                    continue;
                }

                $resolved_seeder_files_[] = $seeder_file_->getPathname();
            }
        }

        sort($resolved_seeder_files_);

        return array_values(array_unique($resolved_seeder_files_));
    }

    private function seeder_class_name_resolve_(string $seeder_file_): ?string
    {
        $seeder_file_content_ = File::get($seeder_file_);
        $namespace_name_ = null;
        $class_name_ = null;

        if (preg_match('/^\s*namespace\s+([^;]+);/m', $seeder_file_content_, $namespace_matches_) === 1) {
            $namespace_name_ = trim($namespace_matches_[1]);
        }

        if (preg_match('/^\s*(?:final\s+|abstract\s+)?class\s+([A-Za-z_][A-Za-z0-9_]*)/m', $seeder_file_content_, $class_matches_) === 1) {
            $class_name_ = trim($class_matches_[1]);
        }

        if ($class_name_ === null || $class_name_ === '') {
            return null;
        }

        if ($namespace_name_ === null || $namespace_name_ === '') {
            return $class_name_;
        }

        return $namespace_name_.'\\'.$class_name_;
    }

    /**
     * @param  array<int, string>  $seeder_classes_
     */
    private function seeders_run_(array $seeder_classes_, ?string $database_connection_): int
    {
        foreach ($seeder_classes_ as $seeder_class_) {
            $seed_command_options_ = [
                '--class' => $seeder_class_,
            ];

            if ($database_connection_ !== null && $database_connection_ !== '') {
                $seed_command_options_['--database'] = $database_connection_;
            }

            $exit_code_ = $this->call('db:seed', $seed_command_options_);

            if ($exit_code_ !== self::SUCCESS) {
                return $exit_code_;
            }
        }

        return self::SUCCESS;
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

    private function path_absolute_resolve_(string $path_input_): string
    {
        if ($this->path_is_absolute_($path_input_)) {
            return $path_input_;
        }

        return base_path(str_replace('/', DIRECTORY_SEPARATOR, $path_input_));
    }
}
