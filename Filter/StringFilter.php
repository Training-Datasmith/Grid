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

namespace Sylius\Component\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class StringFilter implements FilterInterface
{
    public const NAME = 'string';

    public const TYPE_EQUAL = 'equal';

    public const TYPE_NOT_EQUAL = 'not_equal';

    public const TYPE_EMPTY = 'empty';

    public const TYPE_NOT_EMPTY = 'not_empty';

    public const TYPE_CONTAINS = 'contains';

    public const TYPE_NOT_CONTAINS = 'not_contains';

    public const TYPE_STARTS_WITH = 'starts_with';

    public const TYPE_ENDS_WITH = 'ends_with';

    public const TYPE_IN = 'in';

    public const TYPE_NOT_IN = 'not_in';

    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        $expressionBuilder = $dataSource->getExpressionBuilder();

        $value = is_array($data) ? $data['value'] ?? null : $data;
        $type = $data['type'] ?? ($options['type'] ?? self::TYPE_CONTAINS);
        $fields = $options['fields'] ?? [$name];

        if (!in_array($type, [self::TYPE_NOT_EMPTY, self::TYPE_EMPTY], true) && '' === trim((string) $value)) {
            return;
        }

        if (1 === count($fields)) {
            $dataSource->restrict($this->getExpression($expressionBuilder, $type, current($fields), $value));

            return;
        }

        $expressions = [];
        foreach ($fields as $field) {
            $expressions[] = $this->getExpression($expressionBuilder, $type, $field, $value);
        }

        if (self::TYPE_NOT_EQUAL === $type) {
            $dataSource->restrict($expressionBuilder->andX(...$expressions));

            return;
        }

        $dataSource->restrict($expressionBuilder->orX(...$expressions));
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    private function getExpression(
        ExpressionBuilderInterface $expressionBuilder,
        string $type,
        string $field,
        string $value
    ) {
        return match ($type) {
            self::TYPE_EQUAL => $expressionBuilder->equals($field, $value),
            self::TYPE_NOT_EQUAL => $expressionBuilder->notEquals($field, $value),
            self::TYPE_EMPTY => $expressionBuilder->isNull($field),
            self::TYPE_NOT_EMPTY => $expressionBuilder->isNotNull($field),
            self::TYPE_CONTAINS => $expressionBuilder->like($field, '%' . $value . '%'),
            self::TYPE_NOT_CONTAINS => $expressionBuilder->notLike($field, '%' . $value . '%'),
            self::TYPE_STARTS_WITH => $expressionBuilder->like($field, $value . '%'),
            self::TYPE_ENDS_WITH => $expressionBuilder->like($field, '%' . $value),
            self::TYPE_IN => $expressionBuilder->in($field, array_map('trim', explode(',', $value))),
            self::TYPE_NOT_IN => $expressionBuilder->notIn($field, array_map('trim', explode(',', $value))),
            default => throw new \InvalidArgumentException(sprintf('Could not get an expression for type "%s"!', $type)),
        };
    }
}
