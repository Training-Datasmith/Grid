<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Definition;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Action;

final class ActionTest extends TestCase
{
    public function testDefaultsAndMutators(): void
    {
        $action = Action::fromNameAndType('view', 'link');

        self::assertSame('view', $action->getName());
        self::assertSame('link', $action->getType());
        self::assertNull($action->getLabel());
        self::assertTrue($action->isEnabled());
        self::assertNull($action->getIcon());
        self::assertSame([], $action->getOptions());
        self::assertSame(100, $action->getPosition());

        $action->setLabel('Read');
        $action->setEnabled(false);
        $action->setIcon('eye');
        $action->setOptions(['route' => 'show']);
        $action->setPosition(5);

        self::assertSame('Read', $action->getLabel());
        self::assertFalse($action->isEnabled());
        self::assertSame('eye', $action->getIcon());
        self::assertSame(['route' => 'show'], $action->getOptions());
        self::assertSame(5, $action->getPosition());
    }
}
