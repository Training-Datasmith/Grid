<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Double;

use Sylius\Component\Grid\Data\ExpressionBuilderInterface;

final class RecordingExpressionBuilder implements ExpressionBuilderInterface
{
    /** @var list<array{method: string, args: array<int, mixed>}> */
    public array $calls = [];

    private int $tokenSequence = 0;

    public function andX(...$expressions)
    {
        $this->record('andX', $expressions);

        return $this->token('andX', $expressions);
    }

    public function orX(...$expressions)
    {
        $this->record('orX', $expressions);

        return $this->token('orX', $expressions);
    }

    public function comparison(string $field, string $operator, $value)
    {
        $this->record('comparison', [$field, $operator, $value]);

        return $this->token('comparison', [$field, $operator, $value]);
    }

    public function equals(string $field, $value)
    {
        $this->record('equals', [$field, $value]);

        return $this->token('equals', [$field, $value]);
    }

    public function notEquals(string $field, $value)
    {
        $this->record('notEquals', [$field, $value]);

        return $this->token('notEquals', [$field, $value]);
    }

    public function lessThan(string $field, $value)
    {
        $this->record('lessThan', [$field, $value]);

        return $this->token('lessThan', [$field, $value]);
    }

    public function lessThanOrEqual(string $field, $value)
    {
        $this->record('lessThanOrEqual', [$field, $value]);

        return $this->token('lessThanOrEqual', [$field, $value]);
    }

    public function greaterThan(string $field, $value)
    {
        $this->record('greaterThan', [$field, $value]);

        return $this->token('greaterThan', [$field, $value]);
    }

    public function greaterThanOrEqual(string $field, $value)
    {
        $this->record('greaterThanOrEqual', [$field, $value]);

        return $this->token('greaterThanOrEqual', [$field, $value]);
    }

    public function in(string $field, array $values)
    {
        $this->record('in', [$field, $values]);

        return $this->token('in', [$field, $values]);
    }

    public function notIn(string $field, array $values)
    {
        $this->record('notIn', [$field, $values]);

        return $this->token('notIn', [$field, $values]);
    }

    public function isNull(string $field)
    {
        $this->record('isNull', [$field]);

        return $this->token('isNull', [$field]);
    }

    public function isNotNull(string $field)
    {
        $this->record('isNotNull', [$field]);

        return $this->token('isNotNull', [$field]);
    }

    public function like(string $field, string $pattern)
    {
        $this->record('like', [$field, $pattern]);

        return $this->token('like', [$field, $pattern]);
    }

    public function notLike(string $field, string $pattern)
    {
        $this->record('notLike', [$field, $pattern]);

        return $this->token('notLike', [$field, $pattern]);
    }

    public function orderBy(string $field, string $direction)
    {
        $this->record('orderBy', [$field, $direction]);

        return $this->token('orderBy', [$field, $direction]);
    }

    public function addOrderBy(string $field, string $direction)
    {
        $this->record('addOrderBy', [$field, $direction]);

        return $this->token('addOrderBy', [$field, $direction]);
    }

    /**
     * @param array<int, mixed> $args
     */
    private function record(string $method, array $args): void
    {
        $this->calls[] = ['method' => $method, 'args' => $args];
    }

    /**
     * @param array<int, mixed> $args
     */
    private function token(string $method, array $args): object
    {
        return new \stdClass();
    }
}
