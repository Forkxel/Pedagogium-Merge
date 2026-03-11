<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\MigratorConfiguration;

$databaseUrl = getenv('DATABASE_URL');

if (!$databaseUrl) {
    throw new RuntimeException('DATABASE_URL is not set.');
}

$parts = parse_url($databaseUrl);

if ($parts === false) {
    throw new RuntimeException('Invalid DATABASE_URL format.');
}

$host = $parts['host'] ?? null;
$port = $parts['port'] ?? 5432;
$path = $parts['path'] ?? null;
$user = $parts['user'] ?? null;
$password = $parts['pass'] ?? null;

if ($host === null || $path === null || $user === null || $password === null) {
    throw new RuntimeException('DATABASE_URL is missing required parts.');
}

$dbname = ltrim($path, '/');

if ($dbname === '') {
    throw new RuntimeException('DATABASE_URL is missing database name.');
}

$connection = DriverManager::getConnection([
    'driver' => 'pdo_pgsql',
    'host' => $host,
    'port' => $port,
    'dbname' => $dbname,
    'user' => $user,
    'password' => $password,
]);

$config = new PhpFile(__DIR__ . '/migrations.php');

$dependencyFactory = DependencyFactory::fromConnection(
    $config,
    new ExistingConnection($connection)
);

$dependencyFactory->getMetadataStorage()->ensureInitialized();

$version = $dependencyFactory
    ->getVersionAliasResolver()
    ->resolveVersionAlias('latest');

$plan = $dependencyFactory
    ->getMigrationPlanCalculator()
    ->getPlanUntilVersion($version);

$migratorConfiguration = new MigratorConfiguration();
$migratorConfiguration->setDryRun(false);
$migratorConfiguration->setAllOrNothing(false);

$dependencyFactory
    ->getMigrator()
    ->migrate($plan, $migratorConfiguration);