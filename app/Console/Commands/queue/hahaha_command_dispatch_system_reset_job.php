<?php

namespace App\Console\Commands\queue;

use App\Jobs\hahaha_job_system_reset;
use Illuminate\Console\Command;

class hahaha_command_dispatch_system_reset_job extends Command
{
    protected $signature = 'hahaha:queue:dispatch_system_reset_job';

    protected $description = 'Dispatch the async system reset job to the system queue';

    public function handle(): int
    {
        hahaha_job_system_reset::dispatch();

        $this->components->info('Async system reset job dispatched to [system].');

        return self::SUCCESS;
    }
}
