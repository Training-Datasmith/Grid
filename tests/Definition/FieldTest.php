<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Definition;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Field;

final class FieldTest extends TestCase
{
    public function testDefaultsPathAndLabelToName(): void
    {
        $field = Field::fromNameAndType('enabled', 'boolean');

        self::assertSame('enabled', $field->getPath());
        self::assertSame('enabled', $field->getLabel());
        self::assertTrue($field->isEnabled());
        self::assertNull($field->getSortable());
        self::assertFalse($field->isSortable());
        self::assertSame(100, $field->getPosition());
        self::assertSame([], $field->getOptions());
    }

    public function testSortableStringMakesFieldSortable(): void
    {
        $field = Field::fromNameAndType('enabled', 'boolean');
        $field->setSortable('method.enabled');

        self::assertSame('method.enabled', $field->getSortable());
        self::assertTrue($field->isSortable());
    }

    public function testMutators(): void
    {
        $field = Field::fromNameAndType('enabled', 'boolean');
        $field->setPath('method.enabled');
        $field->setLabel('Is enabled?');
        $field->setEnabled(false);
        $field->setOptions(['foo' => 'bar']);
        $field->setPosition(1);

        self::assertSame('method.enabled', $field->getPath());
        self::assertSame('Is enabled?', $field->getLabel());
        self::assertFalse($field->isEnabled());
        self::assertSame(['foo' => 'bar'], $field->getOptions());
        self::assertSame(1, $field->getPosition());
    }
}
