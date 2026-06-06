<?php

namespace Tests\Feature;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Mockery;
use Tests\TestCase;

class LlibCommandDeleteDatabaseTest extends TestCase
{
    public function test_it_deletes_a_mariadb_database_after_confirmation(): void
    {
        $database_connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        config([
            'database.default' => 'mariadb',
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
            ->times(4)
            ->with('hahaha_install_database_delete_');

        DB::shouldReceive('connection')
            ->times(2)
            ->with('hahaha_install_database_delete_')
            ->andReturn($database_connection_mock_);

        $database_connection_mock_->shouldReceive('getSchemaBuilder')
            ->twice()
            ->andReturn($schema_builder_mock_);

        $schema_builder_mock_->shouldReceive('getSchemas')
            ->once()
            ->andReturn([
                ['name' => 'from'],
            ]);

        $schema_builder_mock_->shouldReceive('dropDatabaseIfExists')
            ->once()
            ->with('from')
            ->andReturnTrue();

        $this->artisan('l_lib:db:delete_database', [
            '--database' => 'from',
            '--connection' => 'mariadb',
            '--force' => '2',
        ])
            ->expectsConfirmation('Do you want to delete database [from]?', 'yes')
            ->expectsOutputToContain('Database deleted: from')
            ->assertSuccessful();
    }

    public function test_it_warns_and_fails_when_database_does_not_exist(): void
    {
        $database_connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        config([
            'database.default' => 'mariadb',
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
            ->with('hahaha_install_database_delete_');

        DB::shouldReceive('connection')
            ->once()
            ->with('hahaha_install_database_delete_')
            ->andReturn($database_connection_mock_);

        $database_connection_mock_->shouldReceive('getSchemaBuilder')
            ->once()
            ->andReturn($schema_builder_mock_);

        $schema_builder_mock_->shouldReceive('getSchemas')
            ->once()
            ->andReturn([
                ['name' => 'another_database'],
            ]);

        $schema_builder_mock_->shouldReceive('dropDatabaseIfExists')->never();

        $this->artisan('l_lib:db:delete_database', [
            '--database' => 'from',
            '--connection' => 'mariadb',
            '--force' => '2',
        ])
            ->expectsOutputToContain('Database does not exist: from')
            ->assertFailed();
    }

    public function test_it_skips_confirmation_when_force_is_one(): void
    {
        $database_connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        config([
            'database.default' => 'mariadb',
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
            ->times(4)
            ->with('hahaha_install_database_delete_');

        DB::shouldReceive('connection')
            ->times(2)
            ->with('hahaha_install_database_delete_')
            ->andReturn($database_connection_mock_);

        $database_connection_mock_->shouldReceive('getSchemaBuilder')
            ->twice()
            ->andReturn($schema_builder_mock_);

        $schema_builder_mock_->shouldReceive('getSchemas')
            ->once()
            ->andReturn([
                ['name' => 'from'],
            ]);

        $schema_builder_mock_->shouldReceive('dropDatabaseIfExists')
            ->once()
            ->with('from')
            ->andReturnTrue();

        $this->artisan('l_lib:db:delete_database', [
            '--database' => 'from',
            '--connection' => 'mariadb',
            '--force' => '1',
        ])
            ->expectsOutputToContain('Database deleted: from')
            ->assertSuccessful();
    }

    public function test_it_fails_when_force_option_is_invalid(): void
    {
        config([
            'database.default' => 'mariadb',
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

        DB::shouldReceive('connection')->never();

        $this->artisan('l_lib:db:delete_database', [
            '--database' => 'from',
            '--connection' => 'mariadb',
            '--force' => '3',
        ])
            ->expectsOutputToContain('The --force option must be 1 or 2.')
            ->assertFailed();
    }

    public function test_it_warns_and_fails_when_sqlite_database_file_does_not_exist(): void
    {
        $database_path_ = base_path('database/missing.sqlite');

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => 'database/missing.sqlite',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ]);

        File::delete($database_path_);

        $this->artisan('l_lib:db:delete_database', [
            '--force' => '2',
        ])
            ->expectsOutputToContain('Database does not exist: '.$database_path_)
            ->assertFailed();
    }
}
