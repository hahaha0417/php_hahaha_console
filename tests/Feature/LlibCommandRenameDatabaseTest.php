<?php

namespace Tests\Feature;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Mockery;
use Tests\TestCase;

class LlibCommandRenameDatabaseTest extends TestCase
{
    public function test_it_renames_a_mariadb_database_after_confirmation(): void
    {
        $connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        config([
            'database.default' => 'mariadb',
            'database.connections.mariadb' => [
                'driver' => 'mariadb',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'from',
                'username' => 'root',
                'password' => 'secret',
            ],
        ]);

        DB::shouldReceive('purge')
            ->times(6)
            ->with('hahaha_install_database_rename_');

        DB::shouldReceive('connection')
            ->times(3)
            ->with('hahaha_install_database_rename_')
            ->andReturn($connection_mock_);

        $connection_mock_->shouldReceive('getSchemaBuilder')
            ->times(3)
            ->andReturn($schema_builder_mock_);

        $schema_builder_mock_->shouldReceive('getSchemas')
            ->twice()
            ->andReturn(
                [['name' => 'from']],
                [['name' => 'another_database']]
            );

        $schema_builder_mock_->shouldReceive('createDatabase')
            ->once()
            ->with('to')
            ->andReturnTrue();

        $schema_builder_mock_->shouldReceive('getTableListing')
            ->once()
            ->with('from', false)
            ->andReturn(['users']);

        $connection_mock_->shouldReceive('unprepared')
            ->once()
            ->with('RENAME TABLE `from`.`users` TO `to`.`users`')
            ->andReturnTrue();

        $schema_builder_mock_->shouldReceive('dropDatabaseIfExists')
            ->once()
            ->with('from')
            ->andReturnTrue();

        $this->artisan('l_lib:db:rename_database', [
            '--from_database' => 'from',
            '--to_database' => 'to',
            '--connection' => 'mariadb',
            '--force' => '2',
        ])
            ->expectsConfirmation('Do you want to rename database [from] to [to]?', 'yes')
            ->expectsOutputToContain('Database renamed: from -> to')
            ->assertSuccessful();
    }

    public function test_it_warns_and_fails_when_source_database_does_not_exist(): void
    {
        $connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        config([
            'database.default' => 'mariadb',
            'database.connections.mariadb' => [
                'driver' => 'mariadb',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'from',
                'username' => 'root',
                'password' => 'secret',
            ],
        ]);

        DB::shouldReceive('purge')
            ->twice()
            ->with('hahaha_install_database_rename_');

        DB::shouldReceive('connection')
            ->once()
            ->with('hahaha_install_database_rename_')
            ->andReturn($connection_mock_);

        $connection_mock_->shouldReceive('getSchemaBuilder')
            ->once()
            ->andReturn($schema_builder_mock_);

        $schema_builder_mock_->shouldReceive('getSchemas')
            ->once()
            ->andReturn([
                ['name' => 'another_database'],
            ]);

        $schema_builder_mock_->shouldReceive('createDatabase')->never();

        $this->artisan('l_lib:db:rename_database', [
            '--from_database' => 'from',
            '--to_database' => 'to',
            '--connection' => 'mariadb',
            '--force' => '2',
        ])
            ->expectsOutputToContain('Source database does not exist: from')
            ->assertFailed();
    }

    public function test_it_warns_and_fails_when_target_database_already_exists(): void
    {
        $connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        config([
            'database.default' => 'mariadb',
            'database.connections.mariadb' => [
                'driver' => 'mariadb',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'from',
                'username' => 'root',
                'password' => 'secret',
            ],
        ]);

        DB::shouldReceive('purge')
            ->times(4)
            ->with('hahaha_install_database_rename_');

        DB::shouldReceive('connection')
            ->times(2)
            ->with('hahaha_install_database_rename_')
            ->andReturn($connection_mock_);

        $connection_mock_->shouldReceive('getSchemaBuilder')
            ->times(2)
            ->andReturn($schema_builder_mock_);

        $schema_builder_mock_->shouldReceive('getSchemas')
            ->twice()
            ->andReturn(
                [['name' => 'from']],
                [['name' => 'to']]
            );

        $schema_builder_mock_->shouldReceive('createDatabase')->never();

        $this->artisan('l_lib:db:rename_database', [
            '--from_database' => 'from',
            '--to_database' => 'to',
            '--connection' => 'mariadb',
            '--force' => '1',
        ])
            ->expectsOutputToContain('Target database already exists: to')
            ->assertFailed();
    }

    public function test_it_warns_and_fails_when_sqlite_source_database_does_not_exist(): void
    {
        $source_database_path_ = base_path('database/missing.sqlite');
        $target_database_path_ = base_path('database/to.sqlite');

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => 'database/missing.sqlite',
            ],
        ]);

        File::delete($source_database_path_);
        File::delete($target_database_path_);

        $this->artisan('l_lib:db:rename_database', [
            '--to_database' => 'database/to.sqlite',
            '--force' => '2',
        ])
            ->expectsOutputToContain('Source database does not exist: '.$source_database_path_)
            ->assertFailed();
    }

    public function test_it_fails_when_force_option_is_invalid(): void
    {
        $this->artisan('l_lib:db:rename_database', [
            '--from_database' => 'from',
            '--to_database' => 'to',
            '--force' => '3',
        ])
            ->expectsOutputToContain('The --force option must be 1 or 2.')
            ->assertFailed();
    }
}
