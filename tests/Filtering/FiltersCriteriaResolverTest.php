<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Filtering;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filtering\FiltersCriteriaResolver;
use Sylius\Component\Grid\Parameters;

final class FiltersCriteriaResolverTest extends TestCase
{
    private FiltersCriteriaResolver $resolver;

    private Grid $grid;

    protected function setUp(): void
    {
        $this->resolver = new FiltersCriteriaResolver();
        $this->grid = Grid::fromCodeAndDriverConfiguration('app', 'array', []);
    }

    public function testHasCriteriaFromDefaultsAndParameters(): void
    {
        self::assertFalse($this->resolver->hasCriteria($this->grid, new Parameters()));

        $filter = Filter::fromNameAndType('favourite', 'string');
        $filter->setCriteria(0);
        $this->grid->addFilter($filter);

        self::assertTrue($this->resolver->hasCriteria($this->grid, new Parameters()));
        self::assertTrue($this->resolver->hasCriteria($this->grid, new Parameters(['criteria' => []])));
    }

    public function testGetCriteriaDefaults(): void
    {
        $filter = Filter::fromNameAndType('favourite', 'string');
        $filter->setCriteria('Pug');
        $this->grid->addFilter($filter);

        self::assertSame(['favourite' => 'Pug'], $this->resolver->getCriteria($this->grid, new Parameters()));
    }

    public function testParameterCriteriaOverridesSameKeyDefault(): void
    {
        $filter = Filter::fromNameAndType('favourite', 'string');
        $filter->setCriteria('Rum');
        $this->grid->addFilter($filter);

        $criteria = $this->resolver->getCriteria($this->grid, new Parameters([
            'criteria' => ['favourite' => 'Pug'],
        ]));

        self::assertSame(['favourite' => 'Pug'], $criteria);
    }
}
