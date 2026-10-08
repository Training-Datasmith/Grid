<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Double;

use Sylius\Component\Grid\Data\ExpressionBuilderInterface;

final class RecordingExpressionBuilder implements ExpressionBuilderInterface
{
    /** @var list<array{method: string, args: array<int, mixed>, token: ExpressionToken}> */
    public array $calls = [];

    private int $tokenSequence = 0;

    public function andX(...$expressions)
    {
        return $this->token('andX', $expressions);
    }

    public function orX(...$expressions)
    {
        return $this->token('orX', $expressions);
    }

    public function comparison(string $field, string $operator, $value)
    {
        return $this->token('comparison', [$field, $operator, $value]);
    }

    public function equals(string $field, $value)
    {
        return $this->token('equals', [$field, $value]);
    }

    public function notEquals(string $field, $value)
    {
        return $this->token('notEquals', [$field, $value]);
    }

    public function lessThan(string $field, $value)
    {
        return $this->token('lessThan', [$field, $value]);
    }

    public function lessThanOrEqual(string $field, $value)
    {
        return $this->token('lessThanOrEqual', [$field, $value]);
    }

    public function greaterThan(string $field, $value)
    {
        return $this->token('greaterThan', [$field, $value]);
    }

    public function greaterThanOrEqual(string $field, $value)
    {
        return $this->token('greaterThanOrEqual', [$field, $value]);
    }

    public function in(string $field, array $values)
    {
        return $this->token('in', [$field, $values]);
    }

    public function notIn(string $field, array $values)
    {
        return $this->token('notIn', [$field, $values]);
    }

    public function isNull(string $field)
    {
        return $this->token('isNull', [$field]);
    }

    public function isNotNull(string $field)
    {
        return $this->token('isNotNull', [$field]);
    }

    public function like(string $field, string $pattern)
    {
        return $this->token('like', [$field, $pattern]);
    }

    public function notLike(string $field, string $pattern)
    {
        return $this->token('notLike', [$field, $pattern]);
    }

    public function orderBy(string $field, string $direction)
    {
        return $this->token('orderBy', [$field, $direction]);
    }

    public function addOrderBy(string $field, string $direction)
    {
        return $this->token('addOrderBy', [$field, $direction]);
    }

    /**
     * @param array<int, mixed> $args
     */
    private function token(string $method, array $args): ExpressionToken
    {
        $token = new ExpressionToken(++$this->tokenSequence, $method, $args);
        $this->calls[] = ['method' => $method, 'args' => $args, 'token' => $token];

        return $token;
    }
}
