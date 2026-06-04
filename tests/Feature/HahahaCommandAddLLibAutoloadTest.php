<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class HahahaCommandAddLLibAutoloadTest extends TestCase
{
    public function test_it_updates_composer_json_and_runs_dump_autoload(): void
    {
        $composer_json_path_ = base_path('composer.json');
        $composer_json_content_ = json_encode([
            'autoload' => [
                'psr-4' => [
                    'App\\' => 'app/',
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
                    && $decoded_content_['autoload']['psr-4']['L_Lib\\'] === 'library/hahaha_laravel_lib/';
            });

        Process::fake([
            '*' => Process::result(
                output: 'generated',
            ),
        ]);

        $this->artisan('hahaha:composer:add_l_lib_autoload')
            ->expectsOutputToContain('composer.json updated and composer dump-autoload completed.')
            ->assertSuccessful();

        Process::assertRan(function ($pending_process_) {
            return $pending_process_->command === [
                'composer',
                'dump-autoload',
            ] && $pending_process_->path === base_path();
        });
    }

    public function test_it_fails_when_composer_json_is_invalid(): void
    {
        File::shouldReceive('get')
            ->once()
            ->with(base_path('composer.json'))
            ->andReturn('{invalid json}');

        File::shouldReceive('put')->never();
        Process::fake();

        $this->artisan('hahaha:composer:add_l_lib_autoload')
            ->expectsOutputToContain('composer.json is not valid JSON.')
            ->assertFailed();

        Process::assertNothingRan();
    }

    public function test_it_fails_when_dump_autoload_fails(): void
    {
        $composer_json_path_ = base_path('composer.json');
        $composer_json_content_ = json_encode([
            'autoload' => [
                'psr-4' => [
                    'App\\' => 'app/',
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
                    && $decoded_content_['autoload']['psr-4']['L_Lib\\'] === 'library/hahaha_laravel_lib/';
            });

        Process::fake([
            '*' => Process::result(
                errorOutput: 'dump failed',
                exitCode: 1,
            ),
        ]);

        $this->artisan('hahaha:composer:add_l_lib_autoload')
            ->expectsOutputToContain('dump failed')
            ->assertFailed();
    }
}
