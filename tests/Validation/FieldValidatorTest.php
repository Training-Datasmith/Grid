<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Validation;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Tests\AssertThrows;
use Sylius\Component\Grid\Validation\FieldValidator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class FieldValidatorTest extends TestCase
{
    public function testUnknownFieldThrowsWithSuggestions(): void
    {
        $validator = new FieldValidator();
        $fields = [
            'name' => Field::fromNameAndType('name', 'string'),
            'code' => Field::fromNameAndType('code', 'string'),
        ];

        $exception = AssertThrows::throwable(BadRequestHttpException::class, function () use ($validator, $fields): void {
            $validator->validateFieldName('non_sortable_field', $fields);
        });

        self::assertStringContainsString('non_sortable_field', $exception->getMessage());
        self::assertStringContainsString('name', $exception->getMessage());
        self::assertStringContainsString('code', $exception->getMessage());
    }

    public function testKnownFieldPasses(): void
    {
        $validator = new FieldValidator();
        $fields = ['name' => Field::fromNameAndType('name', 'string')];

        $validator->validateFieldName('name', $fields);

        $this->expectNotToPerformAssertions();
    }
}
