<?php

declare(strict_types=1);

/**
 * Example: Define a grid programmatically using the array-to-definition converter.
 *
 * In a Sylius application this config would live in a YAML/XML service definition,
 * but it can also be constructed directly from PHP arrays as shown here.
 */

use Sylius\Component\Grid\Definition\ArrayToDefinitionConverter;
use Sylius\Component\Grid\Parameters;

// Typically injected via DI; constructed directly here for illustration.
$converter = new ArrayToDefinitionConverter();

$gridConfig = [
    'driver' => [
        'name'          => 'doctrine/orm',
        'options'       => ['class' => \App\Entity\Product::class],
    ],
    'fields' => [
        'name'  => ['type' => 'string', 'label' => 'Name'],
        'price' => ['type' => 'string', 'label' => 'Price'],
    ],
    'filters' => [
        'search' => [
            'type'    => 'string',
            'label'   => 'Search',
            'options' => ['fields' => ['name']],
        ],
    ],
    'sorting' => ['name' => 'asc'],
];

$grid = $converter->convert('app_product', $gridConfig);

echo 'Grid: '     . $grid->getCode() . PHP_EOL;
echo 'Fields: '   . implode(', ', array_keys($grid->getFields())) . PHP_EOL;
echo 'Filters: '  . implode(', ', array_keys($grid->getFilters())) . PHP_EOL;

// Parameters carry current pagination, filter values and sort order.
$params = new Parameters(['page' => 1, 'limit' => 25]);
echo 'Page: '     . $params->get('page', 1) . PHP_EOL;
