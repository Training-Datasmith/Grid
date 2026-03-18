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

namespace Sylius\Component\Grid\Event;

use Sylius\Component\Grid\Definition\Grid;
use SyliusLabs\Polyfill\Symfony\EventDispatcher\Event;

final class GridDefinitionConverterEvent extends Event
{
    public function __construct(private Grid $grid)
    {
    }

    public function getGrid(): Grid
    {
        return $this->grid;
    }
}
