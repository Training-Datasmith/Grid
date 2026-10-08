<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Double;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Parameters;

final class RecordingDataSource implements DataSourceInterface
{
    public RecordingExpressionBuilder $expressionBuilder;

    /** @var list<array{expression: mixed, condition: string}> */
    public array $restrictions = [];

    /** @param mixed $data */
    public function __construct(private $data = [], ?RecordingExpressionBuilder $expressionBuilder = null)
    {
        $this->expressionBuilder = $expressionBuilder ?? new RecordingExpressionBuilder();
    }

    public function restrict($expression, string $condition = self::CONDITION_AND): void
    {
        $this->restrictions[] = ['expression' => $expression, 'condition' => $condition];
    }

    public function getExpressionBuilder(): RecordingExpressionBuilder
    {
        return $this->expressionBuilder;
    }

    public function getData(Parameters $parameters)
    {
        return $this->data;
    }
}
