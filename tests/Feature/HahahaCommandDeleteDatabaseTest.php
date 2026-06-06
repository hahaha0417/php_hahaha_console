<?php

namespace Tests\Feature;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Mockery;
use Tests\TestCase;

class HahahaCommandDeleteDatabaseTest extends TestCase
{
    public function test_it_deletes_a_mysql_database_from_the_project_env_file(): void
    {
        $database_connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        Config::set('database.default', 'mysql');
        Config::set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'hahaha_octane_codex',
            'username' => 'root',
            'password' => 'secret',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
        ]);

        DB::shouldReceive('purge')
            ->twice()
            ->with('hahaha_install_database_delete_');

        DB::shouldReceive('connection')
            ->once()
            ->with('hahaha_install_database_delete_')
            ->andReturn($database_connection_mock_);

        $database_connection_mock_->shouldReceive('getSchemaBuilder')
            ->once()
            ->andReturn($schema_builder_mock_);

        $schema_builder_mock_->shouldReceive('dropDatabaseIfExists')
            ->once()
            ->with('hahaha_octane_codex')
            ->andReturnTrue();

        $this->artisan('hahaha:install:hahaha_octane_codex:db:delete_database')
            ->expectsOutputToContain('Database deleted: hahaha_octane_codex')
            ->assertSuccessful();
    }

    public function test_it_deletes_a_sqlite_database_file_from_the_project_env_file(): void
    {
        $database_path_ = base_path('database/database.sqlite');
        $original_default_connection_ = config('database.default');
        $original_sqlite_connection_ = config('database.connections.sqlite');

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => 'database/database.sqlite',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        File::delete($database_path_);
        file_put_contents($database_path_, '');

        try {
            $this->artisan('hahaha:install:hahaha_octane_codex:db:delete_database')
                ->expectsOutputToContain('Database deleted at')
                ->assertSuccessful();

            $this->assertFileDoesNotExist($database_path_);
        } finally {
            File::delete($database_path_);
            Config::set('database.default', $original_default_connection_);
            Config::set('database.connections.sqlite', $original_sqlite_connection_);
        }
    }

    public function test_it_fails_when_the_database_connection_is_not_supported(): void
    {
        Config::set('database.default', 'oracle');
        Config::set('database.connections.oracle', [
            'driver' => 'oracle',
            'database' => 'hahaha_octane_codex',
        ]);

        DB::shouldReceive('connection')->never();

        $this->artisan('hahaha:install:hahaha_octane_codex:db:delete_database')
            ->expectsOutputToContain('Unsupported DB_CONNECTION value: oracle')
            ->assertFailed();
    }

    public function test_it_fails_when_the_database_connection_configuration_is_missing(): void
    {
        Config::set('database.default', 'oracle');
        Config::set('database.connections.oracle', null);

        DB::shouldReceive('connection')->never();

        $this->artisan('hahaha:install:hahaha_octane_codex:db:delete_database')
            ->expectsOutputToContain('Unsupported DB_CONNECTION value: oracle')
            ->assertFailed();
    }

    public function test_it_uses_the_database_option_to_override_the_env_database_name(): void
    {
        $database_connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        Config::set('database.default', 'mysql');
        Config::set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'from_env',
            'username' => 'root',
            'password' => 'secret',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
        ]);

        DB::shouldReceive('purge')
            ->twice()
            ->with('hahaha_install_database_delete_');

        DB::shouldReceive('connection')
            ->once()
            ->with('hahaha_install_database_delete_')
            ->andReturn($database_connection_mock_);

        $database_connection_mock_->shouldReceive('getSchemaBuilder')
            ->once()
            ->andReturn($schema_builder_mock_);

        $schema_builder_mock_->shouldReceive('dropDatabaseIfExists')
            ->once()
            ->with('test')
            ->andReturnTrue();

        $this->artisan('hahaha:install:hahaha_octane_codex:db:delete_database', ['--database' => 'test'])
            ->expectsOutputToContain('Database deleted: test')
            ->assertSuccessful();
    }

    public function test_it_uses_the_connection_option_to_override_the_env_connection(): void
    {
        $database_connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        Config::set('database.default', 'mysql');
        Config::set('database.connections.mariadb', [
            'driver' => 'mariadb',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'from_env',
            'username' => 'root',
            'password' => 'secret',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
        ]);

        DB::shouldReceive('purge')
            ->twice()
            ->with('hahaha_install_database_delete_');

        DB::shouldReceive('connection')
            ->once()
            ->with('hahaha_install_database_delete_')
            ->andReturn($database_connection_mock_);

        $database_connection_mock_->shouldReceive('getSchemaBuilder')
            ->once()
            ->andReturn($schema_builder_mock_);

        $schema_builder_mock_->shouldReceive('dropDatabaseIfExists')
            ->once()
            ->with('test')
            ->andReturnTrue();

        $this->artisan('hahaha:install:hahaha_octane_codex:db:delete_database', [
            '--database' => 'test',
            '--connection' => 'mariadb',
        ])
            ->expectsOutputToContain('Database deleted: test')
            ->assertSuccessful();
    }
}
