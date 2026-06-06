<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HahahaCommandGenerateEnvMergeTwoEnvTest extends TestCase
{
    public function test_it_merges_the_specified_base_and_override_env_files_into_the_destination_env_file(): void
    {
        $base_env_file_path_ = base_path('storage/app/base.env');
        $override_env_file_path_ = base_path('storage/app/override.env');
        $destination_env_file_path_ = base_path('library/hahaha_octane_codex/.env.destination');
        $destination_directory_ = dirname($destination_env_file_path_);

        File::shouldReceive('exists')
            ->once()
            ->with($base_env_file_path_)
            ->andReturnTrue();

        File::shouldReceive('exists')
            ->once()
            ->with($override_env_file_path_)
            ->andReturnTrue();

        File::shouldReceive('get')
            ->once()
            ->with($base_env_file_path_)
            ->andReturn(implode(PHP_EOL, [
                'APP_NAME = "Base App"',
                'APP_DESCRIPTION="Base App With Spaces"',
                'APP_ENV  =  local',
                '# keep comment',
                'EMPTY_KEEP=',
                '',
            ]));

        File::shouldReceive('get')
            ->once()
            ->with($override_env_file_path_)
            ->andReturn(implode(PHP_EOL, [
                'APP_NAME="Override App"',
                'APP_DESCRIPTION="Override App With Spaces"',
                'APP_ENV=production',
                'EMPTY_KEEP=',
                'NEW_KEY = "new value"',
                '',
            ]));

        File::shouldReceive('isDirectory')
            ->once()
            ->with($destination_directory_)
            ->andReturnFalse();

        File::shouldReceive('makeDirectory')
            ->once()
            ->with($destination_directory_, 0755, true);

        File::shouldReceive('put')
            ->once()
            ->withArgs(function ($path_, $content_) use ($destination_env_file_path_) {
                return $path_ === $destination_env_file_path_
                    && $content_ === implode(PHP_EOL, [
                        'APP_NAME = "Override App"',
                        'APP_DESCRIPTION="Override App With Spaces"',
                        'APP_ENV  =  production',
                        '# keep comment',
                        'EMPTY_KEEP=',
                        '',
                        'NEW_KEY = "new value"',
                        '',
                    ]);
            });

        $this->artisan('hahaha:install:hahaha_octane_codex:generate_env_merge_two_env', [
            '--base_env' => 'storage/app/base.env',
            '--override_env' => 'storage/app/override.env',
            '--destination_env' => 'library/hahaha_octane_codex/.env.destination',
        ])
            ->expectsOutputToContain('.env file generated at')
            ->assertSuccessful();
    }

    public function test_it_merges_the_specified_base_env_file_and_appends_new_entries_with_original_spacing(): void
    {
        $base_env_file_path_ = base_path('storage/app/base.env');
        $override_env_file_path_ = base_path('storage/app/override.env');
        $destination_env_file_path_ = base_path('library/hahaha_octane_codex/.env.destination');
        $destination_directory_ = dirname($destination_env_file_path_);

        File::shouldReceive('exists')
            ->once()
            ->with($base_env_file_path_)
            ->andReturnTrue();

        File::shouldReceive('exists')
            ->once()
            ->with($override_env_file_path_)
            ->andReturnTrue();

        File::shouldReceive('get')
            ->once()
            ->with($base_env_file_path_)
            ->andReturn(implode(PHP_EOL, [
                ' APP_NAME = base-app',
                'APP_ENV=local',
                '',
            ]));

        File::shouldReceive('get')
            ->once()
            ->with($override_env_file_path_)
            ->andReturn(implode(PHP_EOL, [
                'APP_ENV = production',
                ' APP_EXTRA = extra-value',
                '',
            ]));

        File::shouldReceive('isDirectory')
            ->once()
            ->with($destination_directory_)
            ->andReturnTrue();

        File::shouldReceive('makeDirectory')->never();

        File::shouldReceive('put')
            ->once()
            ->withArgs(function ($path_, $content_) use ($destination_env_file_path_) {
                return $path_ === $destination_env_file_path_
                    && $content_ === implode(PHP_EOL, [
                        ' APP_NAME = base-app',
                        'APP_ENV=production',
                        '',
                        ' APP_EXTRA = extra-value',
                        '',
                    ]);
            });

        $this->artisan('hahaha:install:hahaha_octane_codex:generate_env_merge_two_env', [
            '--base_env' => 'storage/app/base.env',
            '--override_env' => 'storage/app/override.env',
            '--destination_env' => 'library/hahaha_octane_codex/.env.destination',
        ])
            ->expectsOutputToContain('.env file generated at')
            ->assertSuccessful();
    }

    public function test_it_fails_when_the_base_env_file_does_not_exist(): void
    {
        $base_env_file_path_ = base_path('storage/app/base.env');

        File::shouldReceive('exists')
            ->once()
            ->with($base_env_file_path_)
            ->andReturnFalse();

        File::shouldReceive('get')->never();
        File::shouldReceive('put')->never();

        $this->artisan('hahaha:install:hahaha_octane_codex:generate_env_merge_two_env', [
            '--base_env' => 'storage/app/base.env',
            '--override_env' => 'storage/app/override.env',
            '--destination_env' => 'library/hahaha_octane_codex/.env.destination',
        ])
            ->expectsOutputToContain('Base .env file does not exist')
            ->assertFailed();
    }

    public function test_it_fails_when_the_override_env_file_does_not_exist(): void
    {
        $base_env_file_path_ = base_path('storage/app/base.env');
        $override_env_file_path_ = base_path('storage/app/override.env');

        File::shouldReceive('exists')
            ->once()
            ->with($base_env_file_path_)
            ->andReturnTrue();

        File::shouldReceive('exists')
            ->once()
            ->with($override_env_file_path_)
            ->andReturnFalse();

        File::shouldReceive('get')->never();
        File::shouldReceive('put')->never();

        $this->artisan('hahaha:install:hahaha_octane_codex:generate_env_merge_two_env', [
            '--base_env' => 'storage/app/base.env',
            '--override_env' => 'storage/app/override.env',
            '--destination_env' => 'library/hahaha_octane_codex/.env.destination',
        ])
            ->expectsOutputToContain('Override .env file does not exist')
            ->assertFailed();
    }

    public function test_it_fails_when_required_options_are_missing(): void
    {
        File::shouldReceive('exists')->never();
        File::shouldReceive('get')->never();
        File::shouldReceive('put')->never();

        $this->artisan('hahaha:install:hahaha_octane_codex:generate_env_merge_two_env')
            ->expectsOutputToContain('The --base_env option is required.')
            ->assertFailed();
    }
}
