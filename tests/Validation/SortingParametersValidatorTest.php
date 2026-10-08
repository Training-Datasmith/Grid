<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Validation;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Tests\AssertThrows;
use Sylius\Component\Grid\Validation\SortingParametersValidator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class SortingParametersValidatorTest extends TestCase
{
    private SortingParametersValidator $validator;

    /** @var array<string, Field> */
    private array $fields;

    protected function setUp(): void
    {
        $this->validator = new SortingParametersValidator();
        $this->fields = ['name' => Field::fromNameAndType('name', 'string')];
    }

    public function testValidDirectionsPass(): void
    {
        $this->validator->validateSortingParameters(['name' => 'asc'], $this->fields);
        $this->validator->validateSortingParameters(['name' => 'desc'], $this->fields);

        $this->expectNotToPerformAssertions();
    }

    public function testBooleanTrueIsRejected(): void
    {
        AssertThrows::throwable(BadRequestHttpException::class, function (): void {
            $this->validator->validateSortingParameters(['name' => true], $this->fields);
        });
    }

    public function testBooleanFalseIsRejected(): void
    {
        AssertThrows::throwable(BadRequestHttpException::class, function (): void {
            $this->validator->validateSortingParameters(['name' => false], $this->fields);
        });
    }

    public function testInvalidDirectionRejected(): void
    {
        AssertThrows::throwable(BadRequestHttpException::class, function (): void {
            $this->validator->validateSortingParameters(['name' => 'sideways'], $this->fields);
        });
    }
}
