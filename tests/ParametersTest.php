<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Parameters;

final class ParametersTest extends TestCase
{
    public function testAllKeysAndGetRoundTrip(): void
    {
        $parameters = new Parameters(['page' => 2, 'criteria' => ['name' => 'Ada']]);

        self::assertSame(['page' => 2, 'criteria' => ['name' => 'Ada']], $parameters->all());
        self::assertSame(['page', 'criteria'], $parameters->keys());
        self::assertSame(2, $parameters->get('page'));
    }

    public function testHasIsTrueForExplicitNullAndGetReturnsNull(): void
    {
        $parameters = new Parameters(['criteria' => null]);

        self::assertTrue($parameters->has('criteria'));
        self::assertNull($parameters->get('criteria', 'fallback'));
    }

    public function testMissingKeyReturnsDefaultIncludingFalse(): void
    {
        $parameters = new Parameters();

        self::assertFalse($parameters->has('sort'));
        self::assertFalse($parameters->get('sort', false));
    }
}
