<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Double;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Parameters;

final class RecordingDriver implements DriverInterface
{
    /** @var array<string, mixed>|null */
    public ?array $lastConfiguration = null;

    public ?Parameters $lastParameters = null;

    public function __construct(private DataSourceInterface $dataSource)
    {
    }

    public function getDataSource(array $configuration, Parameters $parameters): DataSourceInterface
    {
        $this->lastConfiguration = $configuration;
        $this->lastParameters = $parameters;

        return $this->dataSource;
    }
}
