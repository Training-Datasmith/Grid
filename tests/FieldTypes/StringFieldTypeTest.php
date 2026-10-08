<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\FieldTypes;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\StringFieldType;
use Sylius\Component\Grid\DataExtractor\PropertyAccessDataExtractor;
use Symfony\Component\PropertyAccess\PropertyAccessor;

final class StringFieldTypeTest extends TestCase
{
    public function testRendersViaPropertyAccess(): void
    {
        $type = new StringFieldType(new PropertyAccessDataExtractor(new PropertyAccessor()));
        $field = Field::fromNameAndType('name', 'string');
        $field->setPath('name');

        self::assertSame('Ada', $type->render($field, (object) ['name' => 'Ada'], []));
    }

    public function testEscapesHtml(): void
    {
        $extractor = $this->createMock(DataExtractorInterface::class);
        $field = Field::fromNameAndType('name', 'string');
        $extractor->method('get')->willReturn('<i class="book icon"></i>');

        $type = new StringFieldType($extractor);

        self::assertSame('&lt;i class=&quot;book icon&quot;&gt;&lt;/i&gt;', $type->render($field, [], []));
    }

    public function testCastsScalars(): void
    {
        $extractor = $this->createMock(DataExtractorInterface::class);
        $field = Field::fromNameAndType('name', 'string');

        $type = new StringFieldType($extractor);

        $extractor->method('get')->willReturn(420);
        self::assertSame('420', $type->render($field, [], []));

        $extractor = $this->createMock(DataExtractorInterface::class);
        $extractor->method('get')->willReturn(null);
        $type = new StringFieldType($extractor);
        self::assertSame('', $type->render($field, [], []));
    }
}
