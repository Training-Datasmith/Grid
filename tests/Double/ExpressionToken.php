<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Double;

final class ExpressionToken
{
    public int $id;

    public string $method;

    /** @var array<int, mixed> */
    public array $args;

    /**
     * @param array<int, mixed> $args
     */
    public function __construct(int $id, string $method, array $args)
    {
        $this->id = $id;
        $this->method = $method;
        $this->args = $args;
    }
}
