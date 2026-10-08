<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Definition;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\ActionGroup;
use Sylius\Component\Grid\Tests\AssertThrows;

final class ActionGroupTest extends TestCase
{
    public function testAddGetAndHasAction(): void
    {
        $group = ActionGroup::named('default');
        $action = Action::fromNameAndType('view', 'link');

        self::assertFalse($group->hasAction('view'));
        $group->addAction($action);
        self::assertTrue($group->hasAction('view'));
        self::assertSame($action, $group->getAction('view'));
    }

    public function testDuplicateActionThrows(): void
    {
        $group = ActionGroup::named('default');
        $group->addAction(Action::fromNameAndType('view', 'link'));

        $exception = AssertThrows::throwable(\InvalidArgumentException::class, function () use ($group): void {
            $group->addAction(Action::fromNameAndType('view', 'delete'));
        });

        self::assertStringContainsString('view', $exception->getMessage());
    }

    public function testMissingActionThrows(): void
    {
        $group = ActionGroup::named('default');

        $exception = AssertThrows::throwable(\InvalidArgumentException::class, function () use ($group): void {
            $group->getAction('missing');
        });

        self::assertStringContainsString('missing', $exception->getMessage());
    }
}
