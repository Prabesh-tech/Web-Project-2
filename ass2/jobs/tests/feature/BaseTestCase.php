<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

if (!defined('JOB_CONTROLLER_SKIP_DB_CONNECTION')) {
    define('JOB_CONTROLLER_SKIP_DB_CONNECTION', true);
}

abstract class BaseTestCase extends TestCase
{
    protected function createInMemoryPdo(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON');
        return $pdo;
    }

    protected function createJobSchema(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL
            )'
        );

        $pdo->exec(
            'CREATE TABLE companies (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                companyName TEXT NOT NULL,
                logo TEXT
            )'
        );

        $pdo->exec(
            'CREATE TABLE jobs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT NOT NULL,
                salary TEXT,
                categoryId INTEGER,
                companyId INTEGER,
                location TEXT,
                jobType TEXT,
                closingDate TEXT,
                createdAt TEXT,
                FOREIGN KEY(categoryId) REFERENCES categories(id),
                FOREIGN KEY(companyId) REFERENCES companies(id)
            )'
        );

        $pdo->exec("INSERT INTO categories (name) VALUES ('Software Development')");
        $pdo->exec("INSERT INTO companies (companyName, logo) VALUES ('ABC Tech', 'abc-logo.png')");
    }

    protected function createUserSchema(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                role INTEGER NOT NULL DEFAULT 0,
                createdAt TEXT
            )'
        );
    }
}
