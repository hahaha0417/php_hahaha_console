<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use RuntimeException;
use Throwable;

class hahaha_job_system_reset implements ShouldQueue
{
    use Queueable;

    private const PWSH_PATH = 'C:\\Program Files\\PowerShell\\7\\pwsh.exe';

    private const OCTANE_PROJECT_PATH = '..\\..\\web\\hahaha_octane_codex\\hahaha';

    public int $tries = 3;

    public int $timeout = 300;

    public function __construct()
    {
        $this->onQueue('system');
    }

    public function backoff(): array
    {
        return [1, 5, 10];
    }

    public function handle(): void
    {
        foreach ($this->system_commands_() as $system_command_) {
            $result_ = Process::path($system_command_['path_'])->run($system_command_['command_']);

            if ($result_->failed()) {
                $error_message_ = trim($result_->errorOutput()) ?: trim($result_->output()) ?: 'Command failed.';

                Log::error('System reset job command failed.', [
                    'label' => $system_command_['label_'],
                    'command' => $system_command_['command_'],
                    'error' => $error_message_,
                ]);

                throw new RuntimeException($system_command_['label_'].' failed: '.$error_message_);
            }

            Log::info('System reset job command completed.', [
                'label' => $system_command_['label_'],
                'command' => $system_command_['command_'],
                'output' => trim($result_->output()),
            ]);
        }
    }

    public function failed(?Throwable $exception_): void
    {
        Log::error('System reset job failed.', [
            'exception' => $exception_?->getMessage(),
        ]);
    }

    /**
     * @return array<int, array{label_: string, path_: string, command_: array<int, string>}>
     */
    private function system_commands_(): array
    {
        return [
            [
                'label_' => 'php artisan cache:clear',
                'path_' => base_path(),
                'command_' => [
                    'php',
                    'artisan',
                    'cache:clear',
                ],
            ],
            [
                'label_' => self::OCTANE_PROJECT_PATH.' rr reset',
                'path_' => self::OCTANE_PROJECT_PATH,
                'command_' => [
                    self::PWSH_PATH,
                    '-Command',
                    '.\\rr reset',
                ],
            ],
            [
                'label_' => 'Restart-Service Apache2.4',
                'path_' => base_path(),
                'command_' => [
                    self::PWSH_PATH,
                    '-Command',
                    'Restart-Service Apache2.4',
                ],
            ],
            [
                'label_' => 'Restart-Service mysql',
                'path_' => base_path(),
                'command_' => [
                    self::PWSH_PATH,
                    '-Command',
                    'Restart-Service mysql',
                ],
            ],
        ];
    }
}
