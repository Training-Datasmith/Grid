<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Filter\StringFilter;
use Sylius\Component\Grid\Tests\AssertThrows;
use Sylius\Component\Grid\Tests\Double\RecordingDataSource;

final class StringFilterTest extends TestCase
{
    private StringFilter $filter;

    private RecordingDataSource $dataSource;

    protected function setUp(): void
    {
        $this->filter = new StringFilter();
        $this->dataSource = new RecordingDataSource();
    }

    public function testContainsByDefault(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', 'John', []);

        self::assertSame('like', $this->dataSource->expressionBuilder->calls[0]['method']);
        self::assertSame(['firstName', '%John%'], $this->dataSource->expressionBuilder->calls[0]['args']);
    }

    public function testTypeFromOptionsForScalarValue(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', 'John', ['type' => StringFilter::TYPE_EQUAL]);

        self::assertSame('equals', $this->dataSource->expressionBuilder->calls[0]['method']);
    }

    public function testZeroIsNotBlank(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', ['type' => StringFilter::TYPE_CONTAINS, 'value' => '0'], []);

        self::assertCount(1, $this->dataSource->restrictions);
    }

    public function testBlankValuesDoNotRestrictExceptEmptyTypes(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', ['type' => StringFilter::TYPE_CONTAINS, 'value' => ''], []);
        self::assertCount(0, $this->dataSource->restrictions);

        $this->filter->apply($this->dataSource, 'firstName', ['type' => StringFilter::TYPE_EMPTY], []);
        self::assertCount(1, $this->dataSource->restrictions);
    }

    public function testMultipleFieldsOrAndNotEqualAnd(): void
    {
        $this->filter->apply($this->dataSource, 'name', ['type' => StringFilter::TYPE_CONTAINS, 'value' => 'John'], [
            'fields' => ['firstName', 'lastName'],
        ]);
        self::assertSame('orX', $this->dataSource->expressionBuilder->calls[2]['method']);

        $this->dataSource = new RecordingDataSource();
        $this->filter->apply($this->dataSource, 'name', ['type' => StringFilter::TYPE_NOT_EQUAL, 'value' => 'John'], [
            'fields' => ['firstName', 'lastName'],
        ]);
        self::assertSame('andX', $this->dataSource->expressionBuilder->calls[2]['method']);
    }

    public function testUnknownTypeThrowsWithoutRestricting(): void
    {
        AssertThrows::throwable(\InvalidArgumentException::class, function (): void {
            $this->filter->apply($this->dataSource, 'firstName', ['type' => 'UNKNOWN_TYPE', 'value' => 'John'], []);
        });

        self::assertCount(0, $this->dataSource->restrictions);
    }

    public function testInSplitsAndTrims(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', ['type' => StringFilter::TYPE_IN, 'value' => 'John, Paul,Rick'], []);

        self::assertSame('in', $this->dataSource->expressionBuilder->calls[0]['method']);
        self::assertSame(['John', 'Paul', 'Rick'], $this->dataSource->expressionBuilder->calls[0]['args'][1]);
    }
}
