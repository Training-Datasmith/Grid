<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests;

use PHPUnit\Framework\TestCase;

final class ComposerMetadataTest extends TestCase
{
    public function testOptionsResolverIsRuntimeDependency(): void
    {
        $composer = json_decode((string) file_get_contents(__DIR__ . '/../composer.json'), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('symfony/options-resolver', $composer['require'] ?? []);
        self::assertArrayNotHasKey('symfony/options-resolver', $composer['require-dev'] ?? []);
    }
}
