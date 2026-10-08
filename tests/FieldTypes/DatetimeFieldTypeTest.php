<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\FieldTypes;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\DatetimeFieldType;
use Sylius\Component\Grid\Tests\AssertThrows;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DatetimeFieldTypeTest extends TestCase
{
    public function testFormatsDateTime(): void
    {
        $extractor = $this->createMock(DataExtractorInterface::class);
        $field = Field::fromNameAndType('created', 'datetime');
        $instant = new \DateTimeImmutable('2001-10-10 12:34:56 UTC');
        $extractor->method('get')->willReturn($instant);

        $type = new DatetimeFieldType($extractor);

        self::assertSame('2001-10-10', $type->render($field, [], ['format' => 'Y-m-d']));
    }

    public function testNullReturnsEmptyString(): void
    {
        $extractor = $this->createMock(DataExtractorInterface::class);
        $extractor->method('get')->willReturn(null);
        $type = new DatetimeFieldType($extractor);
        $field = Field::fromNameAndType('created', 'datetime');

        self::assertSame('', $type->render($field, [], ['format' => 'Y-m-d']));
    }

    public function testNonDateTimeThrows(): void
    {
        $extractor = $this->createMock(DataExtractorInterface::class);
        $extractor->method('get')->willReturn('bad');
        $type = new DatetimeFieldType($extractor);
        $field = Field::fromNameAndType('created', 'datetime');

        AssertThrows::throwable(\InvalidArgumentException::class, function () use ($type, $field): void {
            $type->render($field, [], ['format' => 'Y-m-d']);
        });
    }

    public function testConfigureOptionsDefault(): void
    {
        $type = new DatetimeFieldType($this->createMock(DataExtractorInterface::class));
        $resolver = new OptionsResolver();
        $type->configureOptions($resolver);
        $options = $resolver->resolve([]);

        self::assertSame('Y-m-d H:i:s', $options['format']);
    }
}
