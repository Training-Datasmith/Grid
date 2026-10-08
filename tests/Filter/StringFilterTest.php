<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Filter\StringFilter;
use Sylius\Component\Grid\Tests\AssertRestrictions;
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

        AssertRestrictions::assertCount($this->dataSource, 1);
        AssertRestrictions::assertAt($this->dataSource, 0, AssertRestrictions::tokenAt($this->dataSource, 0));
        self::assertSame('like', $this->dataSource->expressionBuilder->calls[0]['method']);
        self::assertSame(['firstName', '%John%'], $this->dataSource->expressionBuilder->calls[0]['args']);
    }

    public function testTypeFromOptionsForScalarValue(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', 'John', ['type' => StringFilter::TYPE_EQUAL]);

        AssertRestrictions::assertAt($this->dataSource, 0, AssertRestrictions::tokenAt($this->dataSource, 0));
        self::assertSame('equals', $this->dataSource->expressionBuilder->calls[0]['method']);
    }

    public function testEqualOperator(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', ['type' => StringFilter::TYPE_EQUAL, 'value' => 'John'], []);

        AssertRestrictions::assertAt($this->dataSource, 0, AssertRestrictions::tokenAt($this->dataSource, 0));
        self::assertSame(['firstName', 'John'], $this->dataSource->expressionBuilder->calls[0]['args']);
    }

    public function testNotEqualOperator(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', ['type' => StringFilter::TYPE_NOT_EQUAL, 'value' => 'John'], []);

        AssertRestrictions::assertAt($this->dataSource, 0, AssertRestrictions::tokenAt($this->dataSource, 0));
        self::assertSame('notEquals', $this->dataSource->expressionBuilder->calls[0]['method']);
    }

    public function testEmptyOperator(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', ['type' => StringFilter::TYPE_EMPTY], []);

        AssertRestrictions::assertAt($this->dataSource, 0, AssertRestrictions::tokenAt($this->dataSource, 0));
        self::assertSame('isNull', $this->dataSource->expressionBuilder->calls[0]['method']);
    }

    public function testNotEmptyOperator(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', ['type' => StringFilter::TYPE_NOT_EMPTY], []);

        AssertRestrictions::assertAt($this->dataSource, 0, AssertRestrictions::tokenAt($this->dataSource, 0));
        self::assertSame('isNotNull', $this->dataSource->expressionBuilder->calls[0]['method']);
    }

    public function testNotContainsOperator(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', ['type' => StringFilter::TYPE_NOT_CONTAINS, 'value' => 'John'], []);

        AssertRestrictions::assertAt($this->dataSource, 0, AssertRestrictions::tokenAt($this->dataSource, 0));
        self::assertSame(['firstName', '%John%'], $this->dataSource->expressionBuilder->calls[0]['args']);
        self::assertSame('notLike', $this->dataSource->expressionBuilder->calls[0]['method']);
    }

    public function testStartsWithOperator(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', ['type' => StringFilter::TYPE_STARTS_WITH, 'value' => 'John'], []);

        AssertRestrictions::assertAt($this->dataSource, 0, AssertRestrictions::tokenAt($this->dataSource, 0));
        self::assertSame(['firstName', 'John%'], $this->dataSource->expressionBuilder->calls[0]['args']);
    }

    public function testEndsWithOperator(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', ['type' => StringFilter::TYPE_ENDS_WITH, 'value' => 'John'], []);

        AssertRestrictions::assertAt($this->dataSource, 0, AssertRestrictions::tokenAt($this->dataSource, 0));
        self::assertSame(['firstName', '%John'], $this->dataSource->expressionBuilder->calls[0]['args']);
    }

    public function testZeroIsNotBlank(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', ['type' => StringFilter::TYPE_CONTAINS, 'value' => '0'], []);

        AssertRestrictions::assertCount($this->dataSource, 1);
        AssertRestrictions::assertAt($this->dataSource, 0, AssertRestrictions::tokenAt($this->dataSource, 0));
    }

    public function testBlankValuesDoNotRestrictExceptEmptyTypes(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', ['type' => StringFilter::TYPE_CONTAINS, 'value' => ''], []);
        self::assertCount(0, $this->dataSource->restrictions);

        $this->filter->apply($this->dataSource, 'firstName', ['type' => StringFilter::TYPE_EMPTY], []);
        AssertRestrictions::assertCount($this->dataSource, 1);
        AssertRestrictions::assertAt($this->dataSource, 0, AssertRestrictions::tokenAt($this->dataSource, 0));
    }

    public function testMultipleFieldsOrAndNotEqualAnd(): void
    {
        $this->filter->apply($this->dataSource, 'name', ['type' => StringFilter::TYPE_CONTAINS, 'value' => 'John'], [
            'fields' => ['firstName', 'lastName'],
        ]);
        AssertRestrictions::assertAt($this->dataSource, 0, AssertRestrictions::tokenAt($this->dataSource, 2));
        self::assertSame('orX', $this->dataSource->expressionBuilder->calls[2]['method']);

        $this->dataSource = new RecordingDataSource();
        $this->filter->apply($this->dataSource, 'name', ['type' => StringFilter::TYPE_NOT_EQUAL, 'value' => 'John'], [
            'fields' => ['firstName', 'lastName'],
        ]);
        AssertRestrictions::assertAt($this->dataSource, 0, AssertRestrictions::tokenAt($this->dataSource, 2));
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

        AssertRestrictions::assertAt($this->dataSource, 0, AssertRestrictions::tokenAt($this->dataSource, 0));
        self::assertSame('in', $this->dataSource->expressionBuilder->calls[0]['method']);
        self::assertSame(['John', 'Paul', 'Rick'], $this->dataSource->expressionBuilder->calls[0]['args'][1]);
    }

    public function testNotInSplitsAndTrims(): void
    {
        $this->filter->apply($this->dataSource, 'firstName', ['type' => StringFilter::TYPE_NOT_IN, 'value' => 'John, Paul'], []);

        AssertRestrictions::assertAt($this->dataSource, 0, AssertRestrictions::tokenAt($this->dataSource, 0));
        self::assertSame('notIn', $this->dataSource->expressionBuilder->calls[0]['method']);
        self::assertSame(['John', 'Paul'], $this->dataSource->expressionBuilder->calls[0]['args'][1]);
    }
}
