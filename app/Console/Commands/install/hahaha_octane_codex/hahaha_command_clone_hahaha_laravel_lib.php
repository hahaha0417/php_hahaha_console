<?php

namespace App\Console\Commands\install\hahaha_octaine_codex;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class hahaha_command_clone_hahaha_laravel_lib extends Command
{
    protected $signature = 'hahaha:hahaha_octaine_codex:git:clone_laravel_lib {target_path_}';

    protected $description = 'Clone the php_hahaha_laravel_lib repository into the specified directory';

    public function handle(): int
    {
        $target_path_ = (string) $this->argument('target_path_');
        $repository_url_ = 'https://github.com/hahaha0417/php_hahaha_laravel_lib.git';

        if (File::exists($target_path_)) {
            $this->components->error('Target directory already exists: '.$target_path_);

            return self::FAILURE;
        }

        $clone_result_ = Process::path(base_path())->run([
            'git',
            'clone',
            $repository_url_,
            $target_path_,
        ]);

        if ($clone_result_->failed()) {
            $this->components->error(trim($clone_result_->errorOutput()) ?: 'Git clone failed.');

            return self::FAILURE;
        }

        $this->components->info('Repository cloned to '.$target_path_);

        return self::SUCCESS;
    }
}
