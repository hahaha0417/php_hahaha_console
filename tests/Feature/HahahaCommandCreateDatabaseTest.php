<?php

namespace Tests\Feature;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Mockery;
use Tests\TestCase;

class HahahaCommandCreateDatabaseTest extends TestCase
{
    public function test_it_creates_a_mysql_database_from_the_laravel_database_configuration(): void
    {
        $database_connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        config([
            'database.default' => 'mysql',
            'database.connections.mysql' => [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'hahaha_octaine_codex',
                'username' => 'root',
                'password' => 'secret',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
            ],
        ]);

        DB::shouldReceive('purge')
            ->twice()
            ->with('hahaha_install_database_create_');

        DB::shouldReceive('connection')
            ->once()
            ->with('hahaha_install_database_create_')
            ->andReturn($database_connection_mock_);

        $database_connection_mock_->shouldReceive('getSchemaBuilder')
            ->once()
            ->andReturn($schema_builder_mock_);

        $schema_builder_mock_->shouldReceive('createDatabase')
            ->once()
            ->with('hahaha_octaine_codex')
            ->andReturnTrue();

        $this->artisan('hahaha:install:hahaha_octaine_codex:db:create_database')
            ->expectsOutputToContain('Database created: hahaha_octaine_codex')
            ->assertSuccessful();
    }

    public function test_it_creates_a_sqlite_database_file_from_the_laravel_database_configuration(): void
    {
        $database_path_ = base_path('database/database.sqlite');

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => 'database/database.sqlite',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ]);

        File::delete($database_path_);

        try {
            $this->artisan('hahaha:install:hahaha_octaine_codex:db:create_database')
                ->expectsOutputToContain('Database created at')
                ->assertSuccessful();

            $this->assertFileExists($database_path_);
        } finally {
            File::delete($database_path_);
        }
    }

    public function test_it_fails_when_the_connection_is_not_configured(): void
    {
        config([
            'database.default' => 'missing_connection',
            'database.connections' => [],
        ]);

        DB::shouldReceive('connection')->never();

        $this->artisan('hahaha:install:hahaha_octaine_codex:db:create_database')
            ->expectsOutputToContain('Database connection is not configured: missing_connection')
            ->assertFailed();
    }

    public function test_it_fails_when_the_database_connection_driver_is_not_supported(): void
    {
        config([
            'database.default' => 'oracle',
            'database.connections.oracle' => [
                'driver' => 'oracle',
                'database' => 'hahaha_octaine_codex',
            ],
        ]);

        DB::shouldReceive('connection')->never();

        $this->artisan('hahaha:install:hahaha_octaine_codex:db:create_database')
            ->expectsOutputToContain('Unsupported DB_CONNECTION value: oracle')
            ->assertFailed();
    }

    public function test_it_uses_the_database_option_to_override_the_configured_database_name(): void
    {
        $database_connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        config([
            'database.default' => 'mysql',
            'database.connections.mysql' => [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'from_config',
                'username' => 'root',
                'password' => 'secret',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
            ],
        ]);

        DB::shouldReceive('purge')
            ->twice()
            ->with('hahaha_install_database_create_');

        DB::shouldReceive('connection')
            ->once()
            ->with('hahaha_install_database_create_')
            ->andReturn($database_connection_mock_);

        $database_connection_mock_->shouldReceive('getSchemaBuilder')
            ->once()
            ->andReturn($schema_builder_mock_);

        $schema_builder_mock_->shouldReceive('createDatabase')
            ->once()
            ->with('test')
            ->andReturnTrue();

        $this->artisan('hahaha:install:hahaha_octaine_codex:db:create_database', ['--database' => 'test'])
            ->expectsOutputToContain('Database created: test')
            ->assertSuccessful();
    }

    public function test_it_uses_the_connection_option_to_override_the_default_connection_name(): void
    {
        $database_connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        config([
            'database.default' => 'mysql',
            'database.connections.mysql' => [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'from_mysql',
                'username' => 'root',
                'password' => 'secret',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
            ],
            'database.connections.mariadb' => [
                'driver' => 'mariadb',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'from_mariadb',
                'username' => 'root',
                'password' => 'secret',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
            ],
        ]);

        DB::shouldReceive('purge')
            ->twice()
            ->with('hahaha_install_database_create_');

        DB::shouldReceive('connection')
            ->once()
            ->with('hahaha_install_database_create_')
            ->andReturn($database_connection_mock_);

        $database_connection_mock_->shouldReceive('getSchemaBuilder')
            ->once()
            ->andReturn($schema_builder_mock_);

        $schema_builder_mock_->shouldReceive('createDatabase')
            ->once()
            ->with('test')
            ->andReturnTrue();

        $this->artisan('hahaha:install:hahaha_octaine_codex:db:create_database', [
            '--database' => 'test',
            '--connection' => 'mariadb',
        ])
            ->expectsOutputToContain('Database created: test')
            ->assertSuccessful();

        $this->assertSame('mariadb', config('database.connections.hahaha_install_database_create_.driver'));
    }
}
