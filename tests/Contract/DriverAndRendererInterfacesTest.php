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

    public function testDataSourceInterfaceParameterCounts(): void
    {
        $restrict = new \ReflectionMethod(DataSourceInterface::class, 'restrict');
        self::assertSame(2, $restrict->getNumberOfParameters());
        self::assertTrue($restrict->getParameters()[1]->isOptional());

        $getData = new \ReflectionMethod(DataSourceInterface::class, 'getData');
        self::assertSame(1, $getData->getNumberOfParameters());
    }

    public function testExpressionBuilderInterfaceMethods(): void
    {
        $reflection = new \ReflectionClass(ExpressionBuilderInterface::class);
        $required = ['andX', 'orX', 'comparison', 'equals', 'notEquals', 'lessThan', 'lessThanOrEqual', 'greaterThan', 'greaterThanOrEqual', 'in', 'notIn', 'isNull', 'isNotNull', 'like', 'notLike', 'orderBy', 'addOrderBy'];

        foreach ($required as $method) {
            self::assertTrue($reflection->hasMethod($method), $method);
        }
    }

    public function testExpressionBuilderParameterCounts(): void
    {
        $equals = new \ReflectionMethod(ExpressionBuilderInterface::class, 'equals');
        self::assertSame(2, $equals->getNumberOfParameters());

        $andX = new \ReflectionMethod(ExpressionBuilderInterface::class, 'andX');
        self::assertTrue($andX->isVariadic());

        $comparison = new \ReflectionMethod(ExpressionBuilderInterface::class, 'comparison');
        self::assertSame(3, $comparison->getNumberOfParameters());
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

    public function testDriverInterfaceParameterCounts(): void
    {
        $getDataSource = new \ReflectionMethod(DriverInterface::class, 'getDataSource');
        self::assertSame(2, $getDataSource->getNumberOfParameters());
    }

    public function testGridRendererParameterCounts(): void
    {
        $render = new \ReflectionMethod(GridRendererInterface::class, 'render');
        self::assertSame(2, $render->getNumberOfParameters());

        $renderField = new \ReflectionMethod(GridRendererInterface::class, 'renderField');
        self::assertSame(3, $renderField->getNumberOfParameters());
    }
}
