<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class HahahaCommandCloneOctaineCodexTest extends TestCase
{
    public function test_it_clones_the_octaine_codex_repository_into_the_specified_relative_directory(): void
    {
        $target_directory_ = base_path('library/hahaha_octaine_codex');

        File::shouldReceive('exists')
            ->once()
            ->with($target_directory_)
            ->andReturnFalse();

        Process::fake([
            '*' => Process::result(
                output: 'cloned',
            ),
        ]);

        $this->artisan('hahaha:git:clone_hahaha_octaine_codex', [
            'target_directory_' => 'library/hahaha_octaine_codex',
        ])
            ->expectsOutputToContain('Repository cloned to')
            ->assertSuccessful();

        Process::assertRan(function ($pending_process_) use ($target_directory_) {
            if (! is_array($pending_process_->command)) {
                return false;
            }

            return $pending_process_->command[0] === 'git'
                && $pending_process_->command[1] === 'clone'
                && $pending_process_->command[2] === 'https://github.com/hahaha0417/php_hahaha_octaine_codex.git'
                && $pending_process_->command[3] === $target_directory_
                && $pending_process_->path === base_path();
        });
    }

    public function test_it_fails_when_the_target_directory_already_exists(): void
    {
        $target_directory_ = base_path('library/hahaha_octaine_codex');

        File::shouldReceive('exists')
            ->once()
            ->with($target_directory_)
            ->andReturnTrue();

        $this->artisan('hahaha:git:clone_hahaha_octaine_codex', [
            'target_directory_' => 'library/hahaha_octaine_codex',
        ])
            ->expectsOutputToContain('Target directory already exists')
            ->assertFailed();

        Process::assertNothingRan();
    }

    public function test_it_fails_when_git_clone_fails(): void
    {
        $target_directory_ = 'C:\\target\\octaine-codex';

        File::shouldReceive('exists')
            ->once()
            ->with($target_directory_)
            ->andReturnFalse();

        Process::fake([
            '*' => Process::result(
                errorOutput: 'clone failed',
                exitCode: 1,
            ),
        ]);

        $this->artisan('hahaha:git:clone_hahaha_octaine_codex', [
            'target_directory_' => $target_directory_,
        ])
            ->expectsOutputToContain('clone failed')
            ->assertFailed();
    }
}
