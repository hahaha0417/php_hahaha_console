<?php

namespace Tests\Feature;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Mockery;
use Tests\TestCase;

class HahahaCommandRenameDatabaseTest extends TestCase
{
    public function test_it_renames_a_mariadb_database_by_creating_the_target_database_moving_tables_and_deleting_the_source_database(): void
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

        $schema_builder_mock_->shouldReceive('createDatabase')
            ->once()
            ->with('to')
            ->andReturnTrue();

        $schema_builder_mock_->shouldReceive('getTableListing')
            ->once()
            ->with('from', false)
            ->andReturn(['users', 'orders']);

        $connection_mock_->shouldReceive('unprepared')
            ->once()
            ->with('RENAME TABLE `from`.`users` TO `to`.`users`')
            ->andReturnTrue();

        $connection_mock_->shouldReceive('unprepared')
            ->once()
            ->with('RENAME TABLE `from`.`orders` TO `to`.`orders`')
            ->andReturnTrue();

        $schema_builder_mock_->shouldReceive('dropDatabaseIfExists')
            ->once()
            ->with('from')
            ->andReturnTrue();

        $this->artisan('hahaha:install:hahaha_octane_codex:db:rename_database', [
            '--from_database' => 'from',
            '--to_database' => 'to',
            '--connection' => 'mariadb',
        ])
            ->expectsOutputToContain('Database renamed: from -> to')
            ->assertSuccessful();
    }

    public function test_it_renames_a_pgsql_database_from_the_configured_connection(): void
    {
        $connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = new class {};

        config([
            'database.default' => 'pgsql',
            'database.connections.pgsql' => [
                'driver' => 'pgsql',
                'host' => '127.0.0.1',
                'port' => '5432',
                'database' => 'from_env',
                'username' => 'postgres',
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

        $connection_mock_->shouldReceive('unprepared')
            ->once()
            ->with('ALTER DATABASE "from_env" RENAME TO "to"')
            ->andReturnTrue();

        $this->artisan('hahaha:install:hahaha_octane_codex:db:rename_database', [
            '--to_database' => 'to',
        ])
            ->expectsOutputToContain('Database renamed: from_env -> to')
            ->assertSuccessful();
    }

    public function test_it_renames_a_sqlite_database_file_from_the_configured_connection(): void
    {
        $source_database_path_ = base_path('database/from.sqlite');
        $target_database_path_ = base_path('database/to.sqlite');

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => 'database/from.sqlite',
            ],
        ]);

        File::delete($target_database_path_);
        File::ensureDirectoryExists(dirname($source_database_path_));
        file_put_contents($source_database_path_, '');

        try {
            $this->artisan('hahaha:install:hahaha_octane_codex:db:rename_database', [
                '--to_database' => 'database/to.sqlite',
            ])
                ->expectsOutputToContain('Database renamed: database/from.sqlite -> database/to.sqlite')
                ->assertSuccessful();

            $this->assertFileDoesNotExist($source_database_path_);
            $this->assertFileExists($target_database_path_);
        } finally {
            File::delete($source_database_path_);
            File::delete($target_database_path_);
        }
    }

    public function test_it_fails_when_the_target_database_option_is_missing(): void
    {
        DB::shouldReceive('connection')->never();

        $this->artisan('hahaha:install:hahaha_octane_codex:db:rename_database')
            ->expectsOutputToContain('The --to_database option is required.')
            ->assertFailed();
    }

    public function test_it_fails_when_the_connection_is_not_configured(): void
    {
        config([
            'database.connections' => [],
        ]);

        DB::shouldReceive('connection')->never();

        $this->artisan('hahaha:install:hahaha_octane_codex:db:rename_database', [
            '--from_database' => 'from',
            '--to_database' => 'to',
            '--connection' => 'missing_connection',
        ])
            ->expectsOutputToContain('Database connection is not configured: missing_connection')
            ->assertFailed();
    }

    public function test_it_uses_the_from_database_option_when_the_connection_database_is_not_configured(): void
    {
        $connection_mock_ = Mockery::mock(Connection::class);
        $schema_builder_mock_ = Mockery::mock(Builder::class);

        config([
            'database.default' => 'mariadb',
            'database.connections.mariadb' => [
                'driver' => 'mariadb',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => '',
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

        $schema_builder_mock_->shouldReceive('createDatabase')
            ->once()
            ->with('to')
            ->andReturnTrue();

        $schema_builder_mock_->shouldReceive('getTableListing')
            ->once()
            ->with('from', false)
            ->andReturn([]);

        $schema_builder_mock_->shouldReceive('dropDatabaseIfExists')
            ->once()
            ->with('from')
            ->andReturnTrue();

        $this->artisan('hahaha:install:hahaha_octane_codex:db:rename_database', [
            '--from_database' => 'from',
            '--to_database' => 'to',
        ])
            ->expectsOutputToContain('Database renamed: from -> to')
            ->assertSuccessful();
    }
}
