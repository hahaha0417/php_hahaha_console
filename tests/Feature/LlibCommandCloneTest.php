<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class LlibCommandCloneTest extends TestCase
{
    public function test_it_clones_the_specified_repository_into_the_specified_relative_directory(): void
    {
        $repository_url_ = 'https://github.com/hahaha0417/php_hahaha_laravel_lib.git';
        $target_path_ = base_path('library/hahaha_test_clone_target');

        File::shouldReceive('exists')
            ->once()
            ->with($target_path_)
            ->andReturnFalse();

        Process::fake([
            '*' => Process::result(
                output: 'cloned',
            ),
        ]);

        $this->artisan('l_lib:git:clone', [
            '--url' => $repository_url_,
            '--path' => 'library/hahaha_test_clone_target',
        ])
            ->expectsOutputToContain('Repository cloned to '.$target_path_)
            ->assertSuccessful();

        Process::assertRan(function ($pending_process_) use ($repository_url_, $target_path_) {
            if (! is_array($pending_process_->command)) {
                return false;
            }

            return $pending_process_->command[0] === 'git'
                && $pending_process_->command[1] === 'clone'
                && $pending_process_->command[2] === $repository_url_
                && $pending_process_->command[3] === $target_path_
                && $pending_process_->path === base_path();
        });
    }

    public function test_it_fails_when_url_option_is_missing(): void
    {
        File::shouldReceive('exists')->never();

        $this->artisan('l_lib:git:clone', [
            '--path' => 'library/hahaha_test_clone_target',
        ])
            ->expectsOutputToContain('The --url option is required.')
            ->assertFailed();

        Process::assertNothingRan();
    }

    public function test_it_fails_when_path_option_is_missing(): void
    {
        File::shouldReceive('exists')->never();

        $this->artisan('l_lib:git:clone', [
            '--url' => 'https://github.com/hahaha0417/php_hahaha_laravel_lib.git',
        ])
            ->expectsOutputToContain('The --path option is required.')
            ->assertFailed();

        Process::assertNothingRan();
    }

    public function test_it_fails_when_the_target_directory_already_exists(): void
    {
        $target_path_ = 'C:\\target\\existing-repository';

        File::shouldReceive('exists')
            ->once()
            ->with($target_path_)
            ->andReturnTrue();

        $this->artisan('l_lib:git:clone', [
            '--url' => 'https://github.com/hahaha0417/php_hahaha_laravel_lib.git',
            '--path' => $target_path_,
        ])
            ->expectsOutputToContain('Target directory already exists: '.$target_path_)
            ->assertFailed();

        Process::assertNothingRan();
    }

    public function test_it_fails_when_git_clone_fails(): void
    {
        $repository_url_ = 'https://github.com/hahaha0417/php_hahaha_laravel_lib.git';
        $target_path_ = 'C:\\target\\clone-failed';

        File::shouldReceive('exists')
            ->once()
            ->with($target_path_)
            ->andReturnFalse();

        Process::fake([
            '*' => Process::result(
                errorOutput: 'clone failed',
                exitCode: 1,
            ),
        ]);

        $this->artisan('l_lib:git:clone', [
            '--url' => $repository_url_,
            '--path' => $target_path_,
        ])
            ->expectsOutputToContain('clone failed')
            ->assertFailed();
    }
}
