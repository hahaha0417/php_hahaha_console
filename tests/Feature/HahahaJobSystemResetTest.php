<?php

namespace Tests\Feature;

use App\Jobs\hahaha_job_system_reset;
use Illuminate\Support\Facades\Process;
use RuntimeException;
use Tests\TestCase;

class HahahaJobSystemResetTest extends TestCase
{
    public function test_it_runs_the_expected_system_reset_commands(): void
    {
        Process::fake([
            '*' => Process::result(
                output: 'ok',
            ),
        ]);

        $job_ = new hahaha_job_system_reset;

        $job_->handle();

        Process::assertRanTimes(fn () => true, 4);

        Process::assertRan(function ($pending_process_) {
            return $pending_process_->command === [
                'php',
                'artisan',
                'cache:clear',
            ] && $pending_process_->path === base_path();
        });

        Process::assertRan(function ($pending_process_) {
            return $pending_process_->command === [
                'C:\\Program Files\\PowerShell\\7\\pwsh.exe',
                '-Command',
                './rr reset',
            ] && $pending_process_->path === base_path();
        });

        Process::assertRan(function ($pending_process_) {
            return $pending_process_->command === [
                'C:\\Program Files\\PowerShell\\7\\pwsh.exe',
                '-Command',
                'Restart-Service Apache2.4',
            ] && $pending_process_->path === base_path();
        });

        Process::assertRan(function ($pending_process_) {
            return $pending_process_->command === [
                'C:\\Program Files\\PowerShell\\7\\pwsh.exe',
                '-Command',
                'Restart-Service mysql',
            ] && $pending_process_->path === base_path();
        });
    }

    public function test_it_throws_when_a_system_reset_command_fails(): void
    {
        Process::fake(function ($pending_process_) {
            if ($pending_process_->command === [
                'php',
                'artisan',
                'cache:clear',
            ]) {
                return Process::result(
                    errorOutput: 'cache clear failed',
                    exitCode: 1,
                );
            }

            return Process::result(
                output: 'ok',
            );
        });

        $job_ = new hahaha_job_system_reset;

        try {
            $job_->handle();

            $this->fail('Expected the job to throw a RuntimeException.');
        } catch (RuntimeException $exception_) {
            $this->assertSame(
                'php artisan cache:clear failed: cache clear failed',
                $exception_->getMessage(),
            );
        }

        Process::assertRanTimes(fn () => true, 1);
    }
}
