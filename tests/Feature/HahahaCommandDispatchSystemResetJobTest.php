<?php

namespace Tests\Feature;

use App\Jobs\hahaha_job_system_reset;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class HahahaCommandDispatchSystemResetJobTest extends TestCase
{
    public function test_it_dispatches_the_system_reset_job_to_the_system_queue(): void
    {
        Bus::fake();

        $this->artisan('hahaha:queue:dispatch_system_reset_job')
            ->expectsOutputToContain('Async system reset job dispatched to [system].')
            ->assertSuccessful();

        Bus::assertDispatched(hahaha_job_system_reset::class, function (hahaha_job_system_reset $job_) {
            return $job_->queue === 'system';
        });
    }
}
