<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Definition;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Filter;

final class FilterTest extends TestCase
{
    public function testDefaults(): void
    {
        $filter = Filter::fromNameAndType('keywords', 'string');

        self::assertSame('keywords', $filter->getName());
        self::assertSame('string', $filter->getType());
        self::assertSame('keywords', $filter->getLabel());
        self::assertTrue($filter->isEnabled());
        self::assertNull($filter->getTemplate());
        self::assertSame([], $filter->getOptions());
        self::assertSame([], $filter->getFormOptions());
        self::assertSame(100, $filter->getPosition());
        self::assertNull($filter->getCriteria());
    }

    public function testLabelMayBeFalse(): void
    {
        $filter = Filter::fromNameAndType('keywords', 'string');
        $filter->setLabel(false);

        self::assertFalse($filter->getLabel());
    }

    public function testCriteriaRoundTrip(): void
    {
        $filter = Filter::fromNameAndType('keywords', 'string');
        $filter->setCriteria(0);
        self::assertSame(0, $filter->getCriteria());

        $filter->setCriteria('true');
        self::assertSame('true', $filter->getCriteria());
    }
}
