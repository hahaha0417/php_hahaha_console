<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LlibCommandCreateProxyVhostTest extends TestCase
{
    public function test_it_appends_a_proxy_virtual_host_block_to_the_target_vhosts_file(): void
    {
        $vhosts_path_ = base_path('tests/Fixtures/httpd-proxy-vhosts.conf');

        File::ensureDirectoryExists(dirname($vhosts_path_));
        File::put($vhosts_path_, '# existing'.PHP_EOL);

        $this->artisan('l_lib:apache:create_proxy_vhost', [
            '--port' => '15000',
            '--error_log' => 'D:/web/log/hahaha_optaine_codex_2/hahaha/error.log',
            '--custom_log' => 'D:/web/log/hahaha_optaine_codex_2/hahaha/access.log',
            '--proxy_pass' => 'http://127.0.0.1:10000/',
            '--proxy_pass_reverse' => 'http://127.0.0.1:10000/',
            '--vhosts_path' => $vhosts_path_,
        ])
            ->expectsOutputToContain('Proxy VirtualHost appended to '.$vhosts_path_)
            ->assertSuccessful();

        $expected_content_ = <<<CONF
# existing

Listen 15000
<VirtualHost *:15000>
    ServerName localhost 


    # 轉發 Header 給後端 Kestrel
    RequestHeader set X-Forwarded-For "%{REMOTE_ADDR}s"
    RequestHeader set X-Forwarded-Proto expr=%{REQUEST_SCHEME}

    ProxyPass / http://127.0.0.1:10000/
    ProxyPassReverse / http://127.0.0.1:10000/

    ErrorLog "D:/web/log/hahaha_optaine_codex_2/hahaha/error.log"
    CustomLog "D:/web/log/hahaha_optaine_codex_2/hahaha/access.log" common
\t

</VirtualHost>
CONF;

        $this->assertSame(
            str_replace(["\r\n", "\r"], "\n", $expected_content_),
            str_replace(["\r\n", "\r"], "\n", File::get($vhosts_path_)),
        );
    }

    public function test_it_requires_confirmation_when_the_target_port_already_exists(): void
    {
        $vhosts_path_ = base_path('tests/Fixtures/httpd-proxy-vhosts-duplicate.conf');

        File::ensureDirectoryExists(dirname($vhosts_path_));
        File::put($vhosts_path_, 'Listen 15000'.PHP_EOL.'<VirtualHost *:15000>'.PHP_EOL.'</VirtualHost>'.PHP_EOL);

        $this->artisan('l_lib:apache:create_proxy_vhost', [
            '--port' => '15000',
            '--error_log' => 'D:/web/log/hahaha_optaine_codex_2/hahaha/error.log',
            '--custom_log' => 'D:/web/log/hahaha_optaine_codex_2/hahaha/access.log',
            '--proxy_pass' => 'http://127.0.0.1:10000/',
            '--proxy_pass_reverse' => 'http://127.0.0.1:10000/',
            '--vhosts_path' => $vhosts_path_,
        ])
            ->expectsConfirmation('VirtualHost already exists for port [15000]. Do you want to overwrite it?', 'no')
            ->expectsOutputToContain('VirtualHost overwrite cancelled.')
            ->assertFailed();
    }

    public function test_it_overwrites_the_target_port_when_force_is_one(): void
    {
        $vhosts_path_ = base_path('tests/Fixtures/httpd-proxy-vhosts-force.conf');

        File::ensureDirectoryExists(dirname($vhosts_path_));
        File::put(
            $vhosts_path_,
            'Listen 15000'.PHP_EOL.
            '<VirtualHost *:15000>'.PHP_EOL.
            '    ServerName old.localhost'.PHP_EOL.
            '    ProxyPass / http://127.0.0.1:9999/'.PHP_EOL.
            '    ProxyPassReverse / http://127.0.0.1:9999/'.PHP_EOL.
            '    ErrorLog "D:/old/error.log"'.PHP_EOL.
            '    CustomLog "D:/old/access.log" common'.PHP_EOL.
            '</VirtualHost>'.PHP_EOL,
        );

        $this->artisan('l_lib:apache:create_proxy_vhost', [
            '--port' => '15000',
            '--error_log' => 'D:/web/log/hahaha_optaine_codex_2/hahaha/error.log',
            '--custom_log' => 'D:/web/log/hahaha_optaine_codex_2/hahaha/access.log',
            '--proxy_pass' => 'http://127.0.0.1:10000/',
            '--proxy_pass_reverse' => 'http://127.0.0.1:10000/',
            '--vhosts_path' => $vhosts_path_,
            '--force' => '1',
        ])
            ->expectsOutputToContain('Proxy VirtualHost overwritten in '.$vhosts_path_)
            ->assertSuccessful();

        $actual_content_ = str_replace(["\r\n", "\r"], "\n", File::get($vhosts_path_));

        $this->assertStringContainsString('Listen 15000', $actual_content_);
        $this->assertStringContainsString('ProxyPass / http://127.0.0.1:10000/', $actual_content_);
        $this->assertStringNotContainsString('ProxyPass / http://127.0.0.1:9999/', $actual_content_);
    }

    public function test_it_fails_when_force_option_is_invalid(): void
    {
        $vhosts_path_ = base_path('tests/Fixtures/httpd-proxy-vhosts-invalid-force.conf');

        File::ensureDirectoryExists(dirname($vhosts_path_));
        File::put($vhosts_path_, '# existing'.PHP_EOL);

        $this->artisan('l_lib:apache:create_proxy_vhost', [
            '--port' => '15000',
            '--error_log' => 'D:/web/log/hahaha_optaine_codex_2/hahaha/error.log',
            '--custom_log' => 'D:/web/log/hahaha_optaine_codex_2/hahaha/access.log',
            '--proxy_pass' => 'http://127.0.0.1:10000/',
            '--proxy_pass_reverse' => 'http://127.0.0.1:10000/',
            '--vhosts_path' => $vhosts_path_,
            '--force' => '3',
        ])
            ->expectsOutputToContain('The --force option must be 1 or 2.')
            ->assertFailed();
    }
}
