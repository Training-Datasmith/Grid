<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Data;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Data\DataSourceProvider;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Data\UnsupportedDriverException;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Tests\AssertThrows;
use Sylius\Component\Grid\Tests\Double\RecordingDataSource;
use Sylius\Component\Grid\Tests\Double\RecordingDriver;
use Sylius\Component\Registry\ServiceRegistry;

final class DataSourceProviderTest extends TestCase
{
    public function testResolvesRegisteredDriver(): void
    {
        $dataSource = new RecordingDataSource();
        $registry = new ServiceRegistry(DriverInterface::class, 'driver');
        $registry->register('doctrine/orm', new RecordingDriver($dataSource));

        $provider = new DataSourceProvider($registry);
        $grid = Grid::fromCodeAndDriverConfiguration('app', 'doctrine/orm', ['resource' => 'book']);
        $parameters = new Parameters(['page' => 3]);

        self::assertSame($dataSource, $provider->getDataSource($grid, $parameters));
    }

    public function testPassesDriverConfigurationAndParametersToDriver(): void
    {
        $dataSource = new RecordingDataSource();
        $driver = new RecordingDriver($dataSource);
        $registry = new ServiceRegistry(DriverInterface::class, 'driver');
        $registry->register('doctrine/orm', $driver);

        $provider = new DataSourceProvider($registry);
        $configuration = ['resource' => 'book', 'repository' => 'App\\BookRepository'];
        $grid = Grid::fromCodeAndDriverConfiguration('app', 'doctrine/orm', $configuration);
        $parameters = new Parameters(['page' => 2, 'limit' => 50]);

        $provider->getDataSource($grid, $parameters);

        self::assertSame($configuration, $driver->lastConfiguration);
        self::assertSame($parameters, $driver->lastParameters);
    }

    public function testUnsupportedDriverThrows(): void
    {
        $provider = new DataSourceProvider(new ServiceRegistry(DriverInterface::class, 'driver'));
        $grid = Grid::fromCodeAndDriverConfiguration('app', 'doctrine/banana', []);

        $exception = AssertThrows::throwable(UnsupportedDriverException::class, function () use ($provider, $grid): void {
            $provider->getDataSource($grid, new Parameters());
        });

        self::assertStringContainsString('doctrine/banana', $exception->getMessage());
    }
}
