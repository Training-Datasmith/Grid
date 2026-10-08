<?php

declare(strict_types=1);

namespace Sylius\Component\Grid\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Configuration\GridConfigurationExtender;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\ArrayGridProvider;
use Sylius\Component\Grid\Tests\AssertThrows;

final class ArrayGridProviderTest extends TestCase
{
    public function testUnknownGridThrows(): void
    {
        $provider = new ArrayGridProvider(new RecordingConverter(), []);

        $exception = AssertThrows::throwable(UndefinedGridException::class, function () use ($provider): void {
            $provider->get('missing');
        });

        self::assertStringContainsString('missing', $exception->getMessage());
    }

    public function testInheritanceMergesAndConverts(): void
    {
        $converter = new RecordingConverter();
        $provider = new ArrayGridProvider($converter, [
            'parent' => ['driver' => ['name' => 'array', 'options' => ['x' => 1]]],
            'child' => ['extends' => 'parent', 'driver' => ['name' => 'array', 'options' => ['y' => 2]]],
        ], new GridConfigurationExtender());

        $grid = $provider->get('child');

        self::assertSame('child', $converter->lastCode);
        self::assertArrayNotHasKey('extends', $converter->lastConfiguration);
        self::assertSame(1, $converter->lastConfiguration['driver']['options']['x']);
        self::assertSame(2, $converter->lastConfiguration['driver']['options']['y']);
        self::assertSame($converter->lastGrid, $grid);
    }

    public function testMissingParentThrows(): void
    {
        $provider = new ArrayGridProvider(new RecordingConverter(), [
            'child' => ['extends' => 'missing', 'driver' => ['name' => 'array', 'options' => []]],
        ]);

        AssertThrows::throwable(\InvalidArgumentException::class, function () use ($provider): void {
            $provider->get('child');
        });
    }
}

final class RecordingConverter implements ArrayToDefinitionConverterInterface
{
    public ?string $lastCode = null;

    /** @var array<string, mixed> */
    public array $lastConfiguration = [];

    public ?Grid $lastGrid = null;

    public function convert(string $code, array $configuration): Grid
    {
        $this->lastCode = $code;
        $this->lastConfiguration = $configuration;
        $this->lastGrid = Grid::fromCodeAndDriverConfiguration(
            $code,
            $configuration['driver']['name'] ?? 'array',
            $configuration['driver']['options'] ?? []
        );

        return $this->lastGrid;
    }
}
