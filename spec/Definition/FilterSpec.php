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

namespace spec\Sylius\Component\Grid\Definition;

use PhpSpec\ObjectBehavior;

final class FilterSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedThrough('fromNameAndType', ['keywords', 'string']);
    }

    public function it_has_name(): void
    {
        $this->getName()->shouldReturn('keywords');
    }

    public function it_has_type(): void
    {
        $this->getType()->shouldReturn('string');
    }

    public function it_has_label_which_defaults_to_name(): void
    {
        $this->getLabel()->shouldReturn('keywords');

        $this->setLabel('Search by keyword');
        $this->getLabel()->shouldReturn('Search by keyword');
    }

    public function it_has_no_template_by_default(): void
    {
        $this->getTemplate()->shouldReturn(null);
    }

    public function its_template_is_mutable(): void
    {
        $this->setTemplate('@SyliusGrid/Filter/template.html.twig');
        $this->getTemplate()->shouldReturn('@SyliusGrid/Filter/template.html.twig');
    }

    public function it_has_no_options_by_default(): void
    {
        $this->getOptions()->shouldReturn([]);
    }

    public function it_can_have_options(): void
    {
        $this->setOptions(['fields' => ['firstName', 'lastName', 'email']]);
        $this->getOptions()->shouldReturn(['fields' => ['firstName', 'lastName', 'email']]);
    }

    public function it_has_last_position_by_default(): void
    {
        $this->getPosition()->shouldReturn(100);
    }

    public function its_position_is_mutable(): void
    {
        $this->setPosition(1);
        $this->getPosition()->shouldReturn(1);
    }

    public function it_has_no_criteria_by_default(): void
    {
        $this->getCriteria()->shouldReturn(null);
    }

    public function its_criteria_is_mutable(): void
    {
        $this->setCriteria('false');
        $this->getCriteria()->shouldReturn('false');

        $this->setCriteria(['type' => 'contains']);
        $this->getCriteria()->shouldReturn(['type' => 'contains']);
    }
}
