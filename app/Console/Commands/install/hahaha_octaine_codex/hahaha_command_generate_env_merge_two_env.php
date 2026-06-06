<?php

namespace App\Console\Commands\install\hahaha_octaine_codex;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class hahaha_command_generate_env_merge_two_env extends Command
{
    protected $signature = 'hahaha:install:hahaha_octaine_codex:generate_env_merge_two_env
        {--base_env= : The base .env file path}
        {--override_env= : The override .env file path}
        {--destination_env= : The destination .env file path}';

    protected $description = 'Merge a base .env file with an override .env file and write the merged result to a destination .env file';

    public function handle(): int
    {
        $base_env_file_path_input_ = (string) $this->option('base_env');
        $override_env_file_path_input_ = (string) $this->option('override_env');
        $destination_env_file_path_input_ = (string) $this->option('destination_env');

        if ($base_env_file_path_input_ === '') {
            $this->components->error('The --base_env option is required.');

            return self::FAILURE;
        }

        if ($override_env_file_path_input_ === '') {
            $this->components->error('The --override_env option is required.');

            return self::FAILURE;
        }

        if ($destination_env_file_path_input_ === '') {
            $this->components->error('The --destination_env option is required.');

            return self::FAILURE;
        }

        $base_env_file_path_ = $this->path_resolve_($base_env_file_path_input_);
        $override_env_file_path_ = $this->path_resolve_($override_env_file_path_input_);
        $destination_env_file_path_ = $this->path_resolve_($destination_env_file_path_input_);
        $destination_directory_ = dirname($destination_env_file_path_);

        if (! File::exists($base_env_file_path_)) {
            $this->components->error('Base .env file does not exist: '.$base_env_file_path_);

            return self::FAILURE;
        }

        if (! File::exists($override_env_file_path_)) {
            $this->components->error('Override .env file does not exist: '.$override_env_file_path_);

            return self::FAILURE;
        }

        $base_env_content_ = File::get($base_env_file_path_);
        $override_env_content_ = File::get($override_env_file_path_);

        if (! File::isDirectory($destination_directory_)) {
            File::makeDirectory($destination_directory_, 0755, true);
        }

        $merged_env_content_ = $this->env_content_merge_($base_env_content_, $override_env_content_);

        File::put($destination_env_file_path_, $merged_env_content_);

        $this->components->info('.env file generated at '.$destination_env_file_path_);

        return self::SUCCESS;
    }

    private function path_resolve_(string $path_input_): string
    {
        if ($this->path_is_absolute_($path_input_)) {
            return $path_input_;
        }

        return base_path($path_input_);
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

    private function env_content_merge_(string $base_env_content_, string $override_env_content_): string
    {
        $base_env_lines_ = preg_split("/\r\n|\n|\r/", $base_env_content_) ?: [];
        $override_env_entries_ = $this->env_entries_parse_($override_env_content_);
        $merged_env_lines_ = [];
        $replaced_keys_ = [];

        foreach ($base_env_lines_ as $base_env_line_) {
            $base_env_line_metadata_ = $this->env_line_metadata_parse_($base_env_line_);

            if ($base_env_line_metadata_ === null) {
                $merged_env_lines_[] = $base_env_line_;

                continue;
            }

            $env_key_ = $base_env_line_metadata_['env_key_'];

            if (! isset($override_env_entries_[$env_key_])) {
                $merged_env_lines_[] = $base_env_line_;

                continue;
            }

            $override_env_entry_ = $override_env_entries_[$env_key_];
            $replaced_keys_[$env_key_] = true;

            $merged_env_lines_[] = $base_env_line_metadata_['prefix_']
                .$base_env_line_metadata_['env_key_']
                .$base_env_line_metadata_['before_equals_']
                .'='
                .$base_env_line_metadata_['after_equals_']
                .$override_env_entry_['env_value_'];
        }

        foreach ($override_env_entries_ as $env_key_ => $override_env_entry_) {
            if (isset($replaced_keys_[$env_key_])) {
                continue;
            }

            $merged_env_lines_[] = $override_env_entry_['original_line_'];
        }

        return implode(PHP_EOL, $merged_env_lines_).PHP_EOL;
    }

    /**
     * @return array<string, array{env_key_: string, env_value_: string, original_line_: string}>
     */
    private function env_entries_parse_(string $env_content_): array
    {
        $env_lines_ = preg_split("/\r\n|\n|\r/", $env_content_) ?: [];
        $env_entries_ = [];

        foreach ($env_lines_ as $env_line_) {
            $env_line_metadata_ = $this->env_line_metadata_parse_($env_line_);

            if ($env_line_metadata_ === null) {
                continue;
            }

            if (! $this->env_value_is_set_($env_line_metadata_['env_value_'])) {
                continue;
            }

            $env_entries_[$env_line_metadata_['env_key_']] = [
                'env_key_' => $env_line_metadata_['env_key_'],
                'env_value_' => $env_line_metadata_['env_value_'],
                'original_line_' => $env_line_,
            ];
        }

        return $env_entries_;
    }

    /**
     * @return array{
     *     prefix_: string,
     *     env_key_: string,
     *     before_equals_: string,
     *     after_equals_: string,
     *     env_value_: string
     * }|null
     */
    private function env_line_metadata_parse_(string $env_line_): ?array
    {
        if (trim($env_line_) === '' || str_starts_with(ltrim($env_line_), '#')) {
            return null;
        }

        if (preg_match('/^(\s*)([A-Za-z_][A-Za-z0-9_]*)(\s*)=(\s*)(.*)$/', $env_line_, $matches_) !== 1) {
            return null;
        }

        return [
            'prefix_' => $matches_[1],
            'env_key_' => $matches_[2],
            'before_equals_' => $matches_[3],
            'after_equals_' => $matches_[4],
            'env_value_' => $matches_[5],
        ];
    }

    private function env_value_is_set_(string $env_value_): bool
    {
        return trim($env_value_) !== '';
    }
}
