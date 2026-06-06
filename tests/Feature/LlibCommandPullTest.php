<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class LlibCommandPullTest extends TestCase
{
    public function test_it_pulls_the_specified_repository_inside_the_specified_relative_directory(): void
    {
        $repository_url_ = 'https://github.com/hahaha0417/php_hahaha_laravel_lib.git';
        $target_path_ = base_path('library/hahaha_test_pull_target');

        File::shouldReceive('isDirectory')
            ->once()
            ->with($target_path_)
            ->andReturnTrue();

        Process::fake([
            '*' => Process::result(
                output: 'pulled',
            ),
        ]);

        $this->artisan('l_lib:git:pull', [
            '--url' => $repository_url_,
            '--path' => 'library/hahaha_test_pull_target',
        ])
            ->expectsOutputToContain('Repository pulled in '.$target_path_)
            ->assertSuccessful();

        Process::assertRan(function ($pending_process_) use ($repository_url_, $target_path_) {
            if (! is_array($pending_process_->command)) {
                return false;
            }

            return $pending_process_->command[0] === 'git'
                && $pending_process_->command[1] === 'pull'
                && $pending_process_->command[2] === $repository_url_
                && $pending_process_->path === $target_path_;
        });
    }

    public function test_it_fails_when_url_option_is_missing(): void
    {
        File::shouldReceive('isDirectory')->never();

        $this->artisan('l_lib:git:pull', [
            '--path' => 'library/hahaha_test_pull_target',
        ])
            ->expectsOutputToContain('The --url option is required.')
            ->assertFailed();

        Process::assertNothingRan();
    }

    public function test_it_fails_when_path_option_is_missing(): void
    {
        File::shouldReceive('isDirectory')->never();

        $this->artisan('l_lib:git:pull', [
            '--url' => 'https://github.com/hahaha0417/php_hahaha_laravel_lib.git',
        ])
            ->expectsOutputToContain('The --path option is required.')
            ->assertFailed();

        Process::assertNothingRan();
    }

    public function test_it_fails_when_the_target_directory_does_not_exist(): void
    {
        $target_path_ = 'C:\\target\\missing-repository';

        File::shouldReceive('isDirectory')
            ->once()
            ->with($target_path_)
            ->andReturnFalse();

        $this->artisan('l_lib:git:pull', [
            '--url' => 'https://github.com/hahaha0417/php_hahaha_laravel_lib.git',
            '--path' => $target_path_,
        ])
            ->expectsOutputToContain('Target directory does not exist: '.$target_path_)
            ->assertFailed();

        Process::assertNothingRan();
    }

    public function test_it_fails_when_git_pull_fails(): void
    {
        $repository_url_ = 'https://github.com/hahaha0417/php_hahaha_laravel_lib.git';
        $target_path_ = 'C:\\target\\pull-failed';

        File::shouldReceive('isDirectory')
            ->once()
            ->with($target_path_)
            ->andReturnTrue();

        Process::fake([
            '*' => Process::result(
                errorOutput: 'pull failed',
                exitCode: 1,
            ),
        ]);

        $this->artisan('l_lib:git:pull', [
            '--url' => $repository_url_,
            '--path' => $target_path_,
        ])
            ->expectsOutputToContain('pull failed')
            ->assertFailed();
    }
}
