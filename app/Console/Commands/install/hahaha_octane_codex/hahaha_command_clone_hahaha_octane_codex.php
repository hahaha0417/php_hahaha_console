<?php

namespace App\Console\Commands\install\hahaha_octane_codex;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class hahaha_command_clone_hahaha_octane_codex extends Command
{
    protected $signature = 'hahaha:git:clone_hahaha_octane_codex {target_directory_ : The target directory for the cloned repository}';

    protected $description = 'Clone the php_hahaha_octane_codex repository into the specified directory';

    public function handle(): int
    {
        $target_directory_input_ = (string) $this->argument('target_directory_');
        $target_directory_ = $this->target_directory_resolve_($target_directory_input_);
        $repository_url_ = 'https://github.com/hahaha0417/php_hahaha_octane_codex.git';

        if (File::exists($target_directory_)) {
            $this->components->error('Target directory already exists: '.$target_directory_);

            return self::FAILURE;
        }

        $clone_result_ = Process::path(base_path())->run([
            'git',
            'clone',
            $repository_url_,
            $target_directory_,
        ]);

        if ($clone_result_->failed()) {
            $this->components->error(trim($clone_result_->errorOutput()) ?: 'Git clone failed.');

            return self::FAILURE;
        }

        $this->components->info('Repository cloned to '.$target_directory_);

        return self::SUCCESS;
    }

    private function target_directory_resolve_(string $target_directory_input_): string
    {
        if ($this->target_directory_is_absolute_($target_directory_input_)) {
            return $target_directory_input_;
        }

        return base_path($target_directory_input_);
    }

    private function target_directory_is_absolute_(string $target_directory_input_): bool
    {
        if ($target_directory_input_ === '') {
            return false;
        }

        if (preg_match('/^[A-Za-z]:[\\\\\\/]/', $target_directory_input_) === 1) {
            return true;
        }

        return str_starts_with($target_directory_input_, '/')
            || str_starts_with($target_directory_input_, '\\');
    }
}
