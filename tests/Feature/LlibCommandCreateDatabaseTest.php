<?php

namespace Tests\Feature;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class LlibCommandCreateDatabaseTest extends TestCase
{
    public function test_it_asks_for_confirmation_before_creating_a_mariadb_database_when_force_is_two(): void
    {
        $database_connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        config([
            'database.default' => 'mariadb',
            'database.connections.mariadb' => [
                'driver' => 'mariadb',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'existing_default_db',
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
            ->times(4)
            ->with('hahaha_install_database_create_');

        DB::shouldReceive('connection')
            ->times(2)
            ->with('hahaha_install_database_create_')
            ->andReturn($database_connection_mock_);

        $database_connection_mock_->shouldReceive('getSchemaBuilder')
            ->twice()
            ->andReturn($schema_builder_mock_);

        $schema_builder_mock_->shouldReceive('getSchemas')
            ->once()
            ->andReturn([
                ['name' => 'existing_default_db'],
            ]);

        $schema_builder_mock_->shouldReceive('createDatabase')
            ->once()
            ->with('from')
            ->andReturnTrue();

        $this->artisan('l_lib:db:create_database', [
            '--database' => 'from',
            '--connection' => 'mariadb',
            '--force' => '2',
        ])
            ->expectsConfirmation('Do you want to create database [from]?', 'yes')
            ->expectsOutputToContain('Database created: from')
            ->assertSuccessful();

        $this->assertNull(config('database.connections.hahaha_install_database_create_.database'));
        $this->assertSame('mariadb', config('database.connections.hahaha_install_database_create_.driver'));
    }

    public function test_it_warns_and_fails_when_the_database_already_exists(): void
    {
        $database_connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        config([
            'database.default' => 'mariadb',
            'database.connections.mariadb' => [
                'driver' => 'mariadb',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'existing_default_db',
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

        $schema_builder_mock_->shouldReceive('getSchemas')
            ->once()
            ->andReturn([
                ['name' => 'from'],
            ]);

        $schema_builder_mock_->shouldReceive('createDatabase')->never();

        $this->artisan('l_lib:db:create_database', [
            '--database' => 'from',
            '--connection' => 'mariadb',
            '--force' => '2',
        ])
            ->expectsOutputToContain('Database already exists: from')
            ->assertFailed();
    }

    public function test_it_warns_and_fails_when_the_database_already_exists_even_if_force_is_enabled(): void
    {
        $database_connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        config([
            'database.default' => 'mariadb',
            'database.connections.mariadb' => [
                'driver' => 'mariadb',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'existing_default_db',
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

        $schema_builder_mock_->shouldReceive('getSchemas')
            ->once()
            ->andReturn([
                ['name' => 'from'],
            ]);

        $schema_builder_mock_->shouldReceive('createDatabase')
            ->never();

        $this->artisan('l_lib:db:create_database', [
            '--database' => 'from',
            '--connection' => 'mariadb',
            '--force' => '1',
        ])
            ->expectsOutputToContain('Database already exists: from')
            ->assertFailed();
    }

    public function test_it_fails_when_force_option_is_invalid(): void
    {
        config([
            'database.default' => 'mariadb',
            'database.connections.mariadb' => [
                'driver' => 'mariadb',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'existing_default_db',
                'username' => 'root',
                'password' => 'secret',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
            ],
        ]);

        DB::shouldReceive('connection')->never();

        $this->artisan('l_lib:db:create_database', [
            '--database' => 'from',
            '--connection' => 'mariadb',
            '--force' => '3',
        ])
            ->expectsOutputToContain('The --force option must be 1 or 2.')
            ->assertFailed();
    }
}
