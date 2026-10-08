<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Double;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Parameters;

final class FixedDriver implements DriverInterface
{
    public function __construct(private DataSourceInterface $dataSource)
    {
    }

    public function getDataSource(array $configuration, Parameters $parameters): DataSourceInterface
    {
        return $this->dataSource;
    }
}
