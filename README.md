# Install
```
composer require izica/bitrix-query-builder
```

# Использование

## Получение элементов
```php
$arItems = BitrixQueryBuilder::elements('furniture_products')
    ->execute();
```

## Применение фильтров, получение свойств
```php
$arItems = BitrixQueryBuilder::elements('furniture_products')
    ->select([
        'NAME',
        'PREVIEW_TEXT'
    ])
    ->withProperties()
    ->filter('ACTIVE', 'Y')
    ->execute();
```

## Получение разделов
```php
$arSections = BitrixQueryBuilder::elements('furniture_products')
    ->execute();
```

## Получение дерева разделов
```php
$arSections = BitrixQueryBuilder::elements('furniture_products')
    ->buildTree()
    ->execute();
```

## Получение разделов с элементами
```php
$arSections = BitrixQueryBuilder::elements('furniture_products')
    ->withItems()
    ->execute();
    
$arSections = BitrixQueryBuilder::elements('furniture_products')
    ->withItems()
    ->buildTree()
    ->execute();
```

## Получение разделов с элементами, которые будут отфильтрованы
```php
$obElements = BitrixQueryBuilder::elements('furniture_products')
    ->select([
        'NAME',
        'PREVIEW_TEXT'
    ])
    ->filter('ACTIVE', 'Y');
    
$arSections = BitrixQueryBuilder::elements('furniture_products')
    ->withItems($obElements)
    ->buildTree()
    ->execute();
```

# API
* BitrixQueryBuilder
    * elements(iblock_code: string)
    * sections(iblock_code: string)
* BitrixQueryBuilderElements
    * order(value: array)
    * limit(value: integer)
    * filter(key: string, value: string | array)
    * select(value: string | array, append: boolean)
    * execute()
* BitrixQueryBuilderElements
    * order(value: array)
    * limit(value: integer)
    * filter(key: string, value: string | array)
    * select(value: string | array, append: boolean)
    * execute()