<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\DataExtractor;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\DataExtractor\PropertyAccessDataExtractor;
use Sylius\Component\Grid\Definition\Field;
use Symfony\Component\PropertyAccess\PropertyAccessor;

final class PropertyAccessDataExtractorTest extends TestCase
{
    public function testExtractsNestedArrayAndObjectProperty(): void
    {
        $extractor = new PropertyAccessDataExtractor(new PropertyAccessor());

        $field = Field::fromNameAndType('name', 'string');
        $field->setPath('[author][name]');

        self::assertSame('Ada', $extractor->get($field, ['author' => ['name' => 'Ada']]));

        $object = new class() {
            public string $name = 'Bob';
        };
        $field->setPath('name');
        self::assertSame('Bob', $extractor->get($field, $object));
    }
}
