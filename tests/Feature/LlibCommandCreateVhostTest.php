<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LlibCommandCreateVhostTest extends TestCase
{
    public function test_it_appends_a_virtual_host_block_to_the_target_vhosts_file(): void
    {
        $vhosts_path_ = base_path('tests/Fixtures/httpd-vhosts.conf');

        File::ensureDirectoryExists(dirname($vhosts_path_));
        File::put($vhosts_path_, '# existing'.PHP_EOL);

        $this->artisan('l_lib:apache:create_vhost', [
            '--port' => '10001',
            '--document_root' => 'D:/web/web/hahaha_octane_codex/hahaha/public',
            '--error_log' => 'D:/web/log/hahaha_octane_codex/hahaha/error.log',
            '--custom_log' => 'D:/web/log/hahaha_octane_codex/hahaha/access.log',
            '--directory' => 'D:/web/web/hahaha_octane_codex/hahaha',
            '--vhosts_path' => $vhosts_path_,
        ])
            ->expectsOutputToContain('VirtualHost appended to '.$vhosts_path_)
            ->assertSuccessful();

        $expected_content_ = <<<CONF
# existing

Listen 10001
<VirtualHost *:10001>
    ServerAdmin webmaster@dummy-host.example.com
\tDocumentRoot "D:/web/web/hahaha_octane_codex/hahaha/public"
    ServerName dummy-host.example.com
    ServerAlias www.dummy-host.example.com
    ErrorLog "D:/web/log/hahaha_octane_codex/hahaha/error.log"
    CustomLog "D:/web/log/hahaha_octane_codex/hahaha/access.log" common
\t<Directory "D:/web/web/hahaha_octane_codex/hahaha">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

</VirtualHost>
CONF;

        $this->assertSame(
            str_replace(["\r\n", "\r"], "\n", $expected_content_),
            str_replace(["\r\n", "\r"], "\n", File::get($vhosts_path_)),
        );
    }

    public function test_it_fails_when_the_target_port_already_exists(): void
    {
        $vhosts_path_ = base_path('tests/Fixtures/httpd-vhosts-duplicate.conf');

        File::ensureDirectoryExists(dirname($vhosts_path_));
        File::put($vhosts_path_, 'Listen 10001'.PHP_EOL.'<VirtualHost *:10001>'.PHP_EOL.'</VirtualHost>'.PHP_EOL);

        $this->artisan('l_lib:apache:create_vhost', [
            '--port' => '10001',
            '--document_root' => 'D:/web/web/hahaha_octane_codex/hahaha/public',
            '--error_log' => 'D:/web/log/hahaha_octane_codex/hahaha/error.log',
            '--custom_log' => 'D:/web/log/hahaha_octane_codex/hahaha/access.log',
            '--directory' => 'D:/web/web/hahaha_octane_codex/hahaha',
            '--vhosts_path' => $vhosts_path_,
        ])
            ->expectsConfirmation('VirtualHost already exists for port [10001]. Do you want to overwrite it?', 'no')
            ->expectsOutputToContain('VirtualHost overwrite cancelled.')
            ->assertFailed();
    }

    public function test_it_overwrites_the_target_port_when_force_is_one(): void
    {
        $vhosts_path_ = base_path('tests/Fixtures/httpd-vhosts-force.conf');

        File::ensureDirectoryExists(dirname($vhosts_path_));
        File::put(
            $vhosts_path_,
            'Listen 10001'.PHP_EOL.
            '<VirtualHost *:10001>'.PHP_EOL.
            '    ServerAdmin old@example.com'.PHP_EOL.
            '    DocumentRoot "D:/old/public"'.PHP_EOL.
            '    ServerName old.example.com'.PHP_EOL.
            '    ServerAlias www.old.example.com'.PHP_EOL.
            '    ErrorLog "D:/old/error.log"'.PHP_EOL.
            '    CustomLog "D:/old/access.log" common'.PHP_EOL.
            '    <Directory "D:/old">'.PHP_EOL.
            '        Options Indexes FollowSymLinks'.PHP_EOL.
            '        AllowOverride All'.PHP_EOL.
            '        Require all granted'.PHP_EOL.
            '    </Directory>'.PHP_EOL.
            '</VirtualHost>'.PHP_EOL,
        );

        $this->artisan('l_lib:apache:create_vhost', [
            '--port' => '10001',
            '--document_root' => 'D:/web/web/hahaha_octane_codex/hahaha/public',
            '--error_log' => 'D:/web/log/hahaha_octane_codex/hahaha/error.log',
            '--custom_log' => 'D:/web/log/hahaha_octane_codex/hahaha/access.log',
            '--directory' => 'D:/web/web/hahaha_octane_codex/hahaha',
            '--vhosts_path' => $vhosts_path_,
            '--force' => '1',
        ])
            ->expectsOutputToContain('VirtualHost overwritten in '.$vhosts_path_)
            ->assertSuccessful();

        $actual_content_ = str_replace(["\r\n", "\r"], "\n", File::get($vhosts_path_));

        $this->assertStringContainsString('Listen 10001', $actual_content_);
        $this->assertStringContainsString('DocumentRoot "D:/web/web/hahaha_octane_codex/hahaha/public"', $actual_content_);
        $this->assertStringNotContainsString('DocumentRoot "D:/old/public"', $actual_content_);
    }

    public function test_it_fails_when_force_option_is_invalid(): void
    {
        $vhosts_path_ = base_path('tests/Fixtures/httpd-vhosts-invalid-force.conf');

        File::ensureDirectoryExists(dirname($vhosts_path_));
        File::put($vhosts_path_, '# existing'.PHP_EOL);

        $this->artisan('l_lib:apache:create_vhost', [
            '--port' => '10001',
            '--document_root' => 'D:/web/web/hahaha_octane_codex/hahaha/public',
            '--error_log' => 'D:/web/log/hahaha_octane_codex/hahaha/error.log',
            '--custom_log' => 'D:/web/log/hahaha_octane_codex/hahaha/access.log',
            '--directory' => 'D:/web/web/hahaha_octane_codex/hahaha',
            '--vhosts_path' => $vhosts_path_,
            '--force' => '3',
        ])
            ->expectsOutputToContain('The --force option must be 1 or 2.')
            ->assertFailed();
    }
}
