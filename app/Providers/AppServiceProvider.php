<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;
use PDOException;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Only run this in local/development environments
        if (app()->environment(['local', 'development'])) {
            $this->createDatabaseIfNotExists();
            $this->runMigrationsIfNeeded();
        }
    }

    /**
     * Create the database if it does not exist.
     */
    protected function createDatabaseIfNotExists(): void
    {
        // Get DB config values
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port', 3306);

        try {
            // Connect to MySQL without selecting a database
            $pdo = new PDO("mysql:host=$dbHost;port=$dbPort", $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Check if database exists
            $stmt = $pdo->query("SHOW DATABASES LIKE '$dbName'");
            if ($stmt->rowCount() === 0) {
                $pdo->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                Log::info("Database `$dbName` created successfully.");
            } else {
                Log::info("Database `$dbName` already exists.");
            }
        } catch (PDOException $e) {
            Log::error("Failed to check or create database: " . $e->getMessage());
        }
    }

    /**
     * Run migrations if the migrations table doesn't exist.
     */
    protected function runMigrationsIfNeeded(): void
    {
        try {
            // Now try to connect with the selected database
            DB::connection()->getPdo();

            if (!Schema::hasTable('migrations')) {
                Artisan::call('migrate', ['--force' => true]);
                Log::info("Migrations ran successfully.");
                // Run seeder
                Artisan::call('db:seed', ['--force' => true]);
                Log::info("✅ Seeders ran successfully.");
            } else {
                Log::info("Migrations already applied.");
            }
        } catch (PDOException $e) {
            Log::error("Failed to connect to DB or run migrations: " . $e->getMessage());
        }
    }
}
