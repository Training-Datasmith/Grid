<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\View;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Grid\View\GridViewFactory;

final class GridViewFactoryTest extends TestCase
{
    public function testCreateBuildsViewFromProvider(): void
    {
        $grid = Grid::fromCodeAndDriverConfiguration('app', 'array', []);
        $parameters = new Parameters();
        $provider = new class() implements DataProviderInterface {
            public function getData(Grid $grid, Parameters $parameters)
            {
                return ['alpha', 'beta'];
            }
        };

        $factory = new GridViewFactory($provider);
        $view = $factory->create($grid, $parameters);

        self::assertInstanceOf(GridView::class, $view);
        self::assertSame(['alpha', 'beta'], $view->getData());
        self::assertSame($grid, $view->getDefinition());
        self::assertSame($parameters, $view->getParameters());
    }
}
