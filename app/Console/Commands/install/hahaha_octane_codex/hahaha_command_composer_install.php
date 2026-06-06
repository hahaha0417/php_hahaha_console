<?php

namespace App\Console\Commands\install\hahaha_octaine_codex;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class hahaha_command_composer_install extends Command
{
    protected $signature = 'hahaha:install:hahaha_octaine_codex:composer_install
        {project_path_ : The project path where composer install should run}
        {--no_dev=2 : 1 adds --no-dev, 2 does not add --no-dev}';

    protected $description = 'Run composer install with optional --no-dev';

    public function handle(): int
    {
        $project_path_input_ = (string) $this->argument('project_path_');
        $no_dev_option_ = (string) $this->option('no_dev');
        $project_path_ = $this->path_resolve_($project_path_input_);

        if (! File::isDirectory($project_path_)) {
            $this->components->error('Project path does not exist: '.$project_path_);

            return self::FAILURE;
        }

        if (! in_array($no_dev_option_, ['1', '2'], true)) {
            $this->components->error('The --no_dev option must be 1 or 2.');

            return self::FAILURE;
        }

        $composer_command_ = [
            'composer',
            'install',
            '--ansi',
        ];

        if ($no_dev_option_ === '1') {
            $composer_command_[] = '--no-dev';
        }

        $composer_install_result_ = Process::path($project_path_)->forever()->run(
            $composer_command_,
            function (string $type_, string $buffer_): void {
                $this->output->write($buffer_);
            }
        );

        if ($composer_install_result_->failed()) {
            $this->components->error(trim($composer_install_result_->errorOutput()) ?: 'composer install failed.');

            return self::FAILURE;
        }

        $this->components->info('composer install completed.');

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
}
