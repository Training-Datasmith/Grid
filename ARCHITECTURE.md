# Grid Architecture

## Purpose

A framework-agnostic data-grid library originally built for Sylius.  Provides
filterable, sortable, paginated data tables driven by any Doctrine-compatible
data source.

## Directory Structure

```
Configuration/
  GridConfigurationExtender.php          — merges extra grid config into a base grid
  GridConfigurationExtenderInterface.php

Data/
  DataProvider.php                       — fetches a paginated slice from a data source
  DataProviderInterface.php
  DataSourceInterface.php                — wraps a query builder or collection
  DataSourceProvider.php                 — resolves a DataSource for a given resource
  DataSourceProviderInterface.php
  DriverInterface.php                    — abstracts underlying query builder (Doctrine, etc.)
  ExpressionBuilderInterface.php         — builds driver-specific filter expressions
  UnsupportedDriverException.php

DataExtractor/
  DataExtractorInterface.php
  PropertyAccessDataExtractor.php        — reads field values via Symfony PropertyAccess

Definition/
  Action.php / ActionGroup.php           — bulk/row action definitions
  ArrayToDefinitionConverter.php         — builds a Grid from a plain PHP array config
  Field.php / Filter.php / Grid.php      — immutable definition value objects

Event/
  GridDefinitionConverterEvent.php       — dispatched when a grid definition is built

Exception/
  UndefinedGridException.php

FieldTypes/
  DatetimeFieldType.php / StringFieldType.php — format field values for display

Filter/
  BooleanFilter.php / DateFilter.php / EntityFilter.php
  ExistsFilter.php / MoneyFilter.php / SelectFilter.php / StringFilter.php

Filtering/
  FiltersApplicator.php                  — applies active filter criteria to a DataSource
  FiltersCriteriaResolver.php            — resolves filter values from request parameters

Parameters.php                           — holds pagination + filter + sorting state
Provider/
  ArrayGridProvider.php                  — returns grids from a static array map
  ChainProvider.php                      — delegates to multiple providers in order

Renderer/
  GridRendererInterface.php              — renders a GridView (Twig, etc.)
  BulkActionGridRendererInterface.php

Sorting/
  Sorter.php                             — applies sorting to a DataSource
  SorterInterface.php

Validation/
  FieldValidator.php                     — ensures field definitions are complete
  SortingParametersValidator.php         — validates that requested sort fields exist

View/
  GridView.php / GridViewFactory.php     — builds the final view-model for rendering
```

## Key Design Decisions

- **Definition-first**: grids are described as immutable value objects
  (`Grid`, `Field`, `Filter`, `Action`); runtime state lives separately in
  `Parameters`.
- **Driver abstraction**: `DriverInterface` + `ExpressionBuilderInterface`
  decouple filter logic from Doctrine — any query builder can be wrapped.
- **Event-driven config**: `GridDefinitionConverterEvent` lets bundles mutate a
  grid's definition at runtime before rendering.
- **Array config support**: `ArrayToDefinitionConverter` allows YAML/PHP-array
  grid configuration without writing PHP classes.

## Extension Points

- Implement `DriverInterface` + `ExpressionBuilderInterface` to support a
  non-Doctrine data source.
- Implement `GridProviderInterface` to load grid definitions from any source
  (database, remote API, etc.).
- Add custom filter types by implementing the filter apply logic and registering
  them as services.

## Dependency Flow

```
Consumer
  └── GridViewFactory::create(Grid, Parameters)
        ├── DataProvider → DataSourceProvider → DriverInterface
        ├── FiltersApplicator → Filter\*  → ExpressionBuilderInterface
        ├── Sorter
        └── GridView  →  GridRendererInterface (Twig, etc.)
```
