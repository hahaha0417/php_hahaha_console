<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class HahahaOctaineCodexCommandAddLLibAutoloadTest extends TestCase
{
    public function test_it_updates_composer_json_for_the_specified_project_and_runs_dump_autoload(): void
    {
        $project_path_ = 'C:\\web\\web\\hahaha_octane_codex';
        $composer_json_path_ = $project_path_.DIRECTORY_SEPARATOR.'composer.json';
        $composer_json_content_ = json_encode([
            'autoload' => [
                'psr-4' => [
                    'App\\' => 'app/',
                ],
                'classmap' => [
                    'database/seeders',
                ],
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        File::shouldReceive('get')
            ->once()
            ->with($composer_json_path_)
            ->andReturn($composer_json_content_);

        File::shouldReceive('put')
            ->once()
            ->withArgs(function ($path_, $content_) use ($composer_json_path_) {
                if ($path_ !== $composer_json_path_) {
                    return false;
                }

                $decoded_content_ = json_decode($content_, true);

                return is_array($decoded_content_)
                    && $decoded_content_['autoload']['psr-4']['App\\'] === 'app/'
                    && $decoded_content_['autoload']['classmap'] === [
                        'database/seeders',
                        'library/hahahalib',
                    ];
            });

        Process::fake([
            '*' => Process::result(
                output: 'generated',
            ),
        ]);

        $this->artisan('hahaha:install:hahaha_octane_codex:composer:add_hahahalib_autoload', [
            'project_path_' => $project_path_,
        ])
            ->expectsOutputToContain('composer.json updated and composer dump-autoload completed.')
            ->assertSuccessful();

        Process::assertRan(function ($pending_process_) use ($project_path_) {
            return $pending_process_->command === [
                'composer',
                'dump-autoload',
            ] && $pending_process_->path === $project_path_;
        });
    }
}
