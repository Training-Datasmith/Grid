<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Contract;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Renderer\BulkActionGridRendererInterface;
use Sylius\Component\Grid\Renderer\GridRendererInterface;

final class DriverAndRendererInterfacesTest extends TestCase
{
    public function testDataSourceInterfaceMethods(): void
    {
        $reflection = new \ReflectionClass(DataSourceInterface::class);

        self::assertTrue($reflection->hasMethod('restrict'));
        self::assertTrue($reflection->hasMethod('getExpressionBuilder'));
        self::assertTrue($reflection->hasMethod('getData'));
    }

    public function testExpressionBuilderInterfaceMethods(): void
    {
        $reflection = new \ReflectionClass(ExpressionBuilderInterface::class);
        $required = ['andX', 'orX', 'comparison', 'equals', 'notEquals', 'lessThan', 'lessThanOrEqual', 'greaterThan', 'greaterThanOrEqual', 'in', 'notIn', 'isNull', 'isNotNull', 'like', 'notLike', 'orderBy', 'addOrderBy'];

        foreach ($required as $method) {
            self::assertTrue($reflection->hasMethod($method), $method);
        }
    }

    public function testDriverAndRendererInterfaces(): void
    {
        self::assertTrue((new \ReflectionClass(DriverInterface::class))->hasMethod('getDataSource'));
        $renderer = new \ReflectionClass(GridRendererInterface::class);
        self::assertTrue($renderer->hasMethod('render'));
        self::assertTrue($renderer->hasMethod('renderField'));
        self::assertTrue($renderer->hasMethod('renderAction'));
        self::assertTrue($renderer->hasMethod('renderFilter'));
        self::assertTrue((new \ReflectionClass(BulkActionGridRendererInterface::class))->hasMethod('renderBulkAction'));
    }
}
