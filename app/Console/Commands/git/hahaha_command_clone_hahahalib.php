<?php

namespace App\Console\Commands\git;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class hahaha_command_clone_hahahalib extends Command
{
    protected $signature = 'hahaha:git:clone_hahahalib';

    protected $description = 'Clone the php_hahahalib repository into library/hahahalib';

    public function handle(): int
    {
        $target_path_ = base_path('library/hahahalib');
        $repository_url_ = 'https://github.com/hahaha0417/php_hahahalib.git';

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
