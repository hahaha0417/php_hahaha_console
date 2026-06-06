<?php

namespace App\Console\Commands\install\hahaha_octane_codex;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class hahaha_command_add_l_lib_autoload extends Command
{
    protected $signature = 'hahaha:install:hahaha_octane_codex:composer:add_l_lib_autoload {project_path_}';

    protected $description = 'Add the L_Lib PSR-4 autoload mapping and run composer dump-autoload';

    public function handle(): int
    {
        $project_path_ = (string) $this->argument('project_path_');
        $composer_json_path_ = $project_path_.DIRECTORY_SEPARATOR.'composer.json';
        $composer_json_content_ = File::get($composer_json_path_);
        $composer_config_ = json_decode($composer_json_content_, true);

        if (! is_array($composer_config_)) {
            $this->components->error('composer.json is not valid JSON.');

            return self::FAILURE;
        }

        if (! isset($composer_config_['autoload']) || ! is_array($composer_config_['autoload'])) {
            $composer_config_['autoload'] = [];
        }

        if (! isset($composer_config_['autoload']['psr-4']) || ! is_array($composer_config_['autoload']['psr-4'])) {
            $composer_config_['autoload']['psr-4'] = [];
        }

        $composer_config_['autoload']['psr-4']['L_Lib\\'] = 'library/hahaha_laravel_lib/';

        $updated_composer_json_ = json_encode(
            $composer_config_,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        if ($updated_composer_json_ === false) {
            $this->components->error('composer.json could not be encoded.');

            return self::FAILURE;
        }

        File::put($composer_json_path_, $updated_composer_json_.PHP_EOL);

        $dump_autoload_result_ = Process::path($project_path_)->run([
            'composer',
            'dump-autoload',
        ]);

        if ($dump_autoload_result_->failed()) {
            $this->components->error(trim($dump_autoload_result_->errorOutput()) ?: 'composer dump-autoload failed.');

            return self::FAILURE;
        }

        $this->components->info('composer.json updated and composer dump-autoload completed.');

        return self::SUCCESS;
    }
}
