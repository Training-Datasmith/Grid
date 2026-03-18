<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Grid\Filtering;

use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

final class FiltersCriteriaResolver implements FiltersCriteriaResolverInterface
{
    public function hasCriteria(Grid $grid, Parameters $parameters): bool
    {
        if ($parameters->has('criteria')) {
            return true;
        }
        return !empty($this->getFiltersDefaultCriteria($grid->getFilters()));
    }

    public function getCriteria(Grid $grid, Parameters $parameters): array
    {
        $defaultCriteria = array_map(
            /** @return mixed */
            fn (Filter $filter) => $filter->getCriteria(),
            $this->getFiltersDefaultCriteria($grid->getFilters())
        );

        return $parameters->get('criteria', $defaultCriteria);
    }

    /**
     * @param Filter[] $filters
     *
     * @return Filter[]
     */
    private function getFiltersDefaultCriteria(array $filters): array
    {
        return array_filter($filters, fn (Filter $filter) => null !== $filter->getCriteria());
    }
}
