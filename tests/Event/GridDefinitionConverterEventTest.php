<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Event;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;

final class GridDefinitionConverterEventTest extends TestCase
{
    public function testGetGridReturnsSameInstance(): void
    {
        $grid = Grid::fromCodeAndDriverConfiguration('app', 'array', []);
        $event = new GridDefinitionConverterEvent($grid);

        self::assertSame($grid, $event->getGrid());
    }
}
