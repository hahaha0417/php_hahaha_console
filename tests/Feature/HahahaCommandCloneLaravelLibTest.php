<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class HahahaCommandCloneLaravelLibTest extends TestCase
{
    public function test_it_clones_the_laravel_library_repository_into_the_library_directory(): void
    {
        File::shouldReceive('exists')
            ->once()
            ->with(base_path('library/hahaha_laravel_lib'))
            ->andReturnFalse();

        Process::fake([
            '*' => Process::result(
                output: 'cloned',
            ),
        ]);

        $this->artisan('hahaha:clone-laravel-lib')
            ->expectsOutputToContain('Repository cloned to')
            ->assertSuccessful();

        Process::assertRan(function ($pending_process_) {
            if (! is_array($pending_process_->command)) {
                return false;
            }

            return $pending_process_->command[0] === 'git'
                && $pending_process_->command[1] === 'clone'
                && $pending_process_->command[2] === 'https://github.com/hahaha0417/php_hahaha_laravel_lib.git'
                && $pending_process_->command[3] === base_path('library/hahaha_laravel_lib')
                && $pending_process_->path === base_path();
        });
    }

    public function test_it_fails_when_the_target_directory_already_exists(): void
    {
        File::shouldReceive('exists')
            ->once()
            ->with(base_path('library/hahaha_laravel_lib'))
            ->andReturnTrue();

        $this->artisan('hahaha:clone-laravel-lib')
            ->expectsOutputToContain('Target directory already exists')
            ->assertFailed();

        Process::assertNothingRan();
    }
}
