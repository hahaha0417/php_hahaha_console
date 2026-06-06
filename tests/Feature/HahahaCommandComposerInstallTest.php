<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class HahahaCommandComposerInstallTest extends TestCase
{
    public function test_it_runs_composer_install_with_no_dev_when_option_is_one(): void
    {
        $project_path_ = base_path('library/hahaha_octaine_codex');

        File::shouldReceive('isDirectory')
            ->once()
            ->with($project_path_)
            ->andReturnTrue();

        Process::fake([
            '*' => Process::result(
                output: 'installed',
            ),
        ]);

        $this->artisan('hahaha:install:hahaha_octaine_codex:composer_install', [
            'project_path_' => 'library/hahaha_octaine_codex',
            '--no_dev' => '1',
        ])
            ->expectsOutputToContain('composer install completed.')
            ->assertSuccessful();

        Process::assertRan(function ($pending_process_) use ($project_path_) {
            return $pending_process_->command === [
                'composer',
                'install',
                '--ansi',
                '--no-dev',
            ] && $pending_process_->path === $project_path_;
        });
    }

    public function test_it_runs_composer_install_without_no_dev_when_option_is_two(): void
    {
        $project_path_ = 'C:\\to\\project_path';

        File::shouldReceive('isDirectory')
            ->once()
            ->with($project_path_)
            ->andReturnTrue();

        Process::fake([
            '*' => Process::result(
                output: 'installed',
            ),
        ]);

        $this->artisan('hahaha:install:hahaha_octaine_codex:composer_install', [
            'project_path_' => $project_path_,
            '--no_dev' => '2',
        ])
            ->expectsOutputToContain('composer install completed.')
            ->assertSuccessful();

        Process::assertRan(function ($pending_process_) use ($project_path_) {
            return $pending_process_->command === [
                'composer',
                'install',
                '--ansi',
            ] && $pending_process_->path === $project_path_;
        });
    }

    public function test_it_fails_when_no_dev_option_is_invalid(): void
    {
        File::shouldReceive('isDirectory')
            ->once()
            ->with(base_path('library/hahaha_octaine_codex'))
            ->andReturnTrue();

        Process::fake();

        $this->artisan('hahaha:install:hahaha_octaine_codex:composer_install', [
            'project_path_' => 'library/hahaha_octaine_codex',
            '--no_dev' => '3',
        ])
            ->expectsOutputToContain('The --no_dev option must be 1 or 2.')
            ->assertFailed();

        Process::assertNothingRan();
    }

    public function test_it_fails_when_composer_install_fails(): void
    {
        $project_path_ = base_path('library/hahaha_octaine_codex');

        File::shouldReceive('isDirectory')
            ->once()
            ->with($project_path_)
            ->andReturnTrue();

        Process::fake([
            '*' => Process::result(
                errorOutput: 'install failed',
                exitCode: 1,
            ),
        ]);

        $this->artisan('hahaha:install:hahaha_octaine_codex:composer_install', [
            'project_path_' => 'library/hahaha_octaine_codex',
            '--no_dev' => '2',
        ])
            ->expectsOutputToContain('install failed')
            ->assertFailed();
    }

    public function test_it_fails_when_project_path_does_not_exist(): void
    {
        $project_path_ = 'C:\\missing\\project_path';

        File::shouldReceive('isDirectory')
            ->once()
            ->with($project_path_)
            ->andReturnFalse();

        Process::fake();

        $this->artisan('hahaha:install:hahaha_octaine_codex:composer_install', [
            'project_path_' => $project_path_,
            '--no_dev' => '2',
        ])
            ->expectsOutputToContain('Project path does not exist')
            ->assertFailed();

        Process::assertNothingRan();
    }
}
