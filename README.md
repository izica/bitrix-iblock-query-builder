## Установка
Установить через composer(или скачать), подключить в php_interface/init.php
```
composer require izica/bitrix-query-builder
```

## Использование

#### Получение элементов инфоблока
```php
$arItems = BitrixQueryBuilder::elements('furniture_products')
    ->filter([
        'ACTIVE' => 'Y',
        'NAME' => '%table%'
    ])
    ->select([
         'NAME',
         'PREVIEW_TEXT',
    ])
    ->properties([
        'AUTHOR',
        'COLOR'
    ])
    ->execute();
```
#### Получение разделов инфоблока
```php
$arSections = BitrixQueryBuilder::sections('furniture_products')
    ->withItems()
    ->buildTree()
    ->execute();
```

## Документация
* [BitrixQueryBuilder](#BitrixQueryBuilder)
    * [elements(iblockCode: string)](#BitrixQueryBuilderelements)
    * [sections(iblockCode: string)](#BitrixQueryBuildersections)
* [BitrixQueryBuilderElement](#BitrixQueryBuilderElement)
    * [order(value: array)](#BitrixQueryBuilderElementorder)
    * [limit(value: int)](#BitrixQueryBuilderElementlimit)
    * [page(page: int, perpage: int)](#BitrixQueryBuilderElementpage)
    * [filter(key: string, value: mixed)](#BitrixQueryBuilderElementfilter)
    * [filter(value: array)](#BitrixQueryBuilderElementfilter)
    * [select(value: mixed, append:bool)](#BitrixQueryBuilderElementselect)
    * [properties(value: array)](#BitrixQueryBuilderElementproperties)
    * [execute()](#BitrixQueryBuilderElementexectute)
* [BitrixQueryBuilderSection](#BitrixQueryBuilderSection)
    * [order(value: array)](#BitrixQueryBuilderSection)
    * [limit(value: int)](#BitrixQueryBuilderSectionlimit)
    * [page(page: int, perpage: int)](#BitrixQueryBuilderSectionpage)
    * [filter(key: string, value: mixed)](#BitrixQueryBuilderSectionfilter)
    * [filter(value: array)](#BitrixQueryBuilderSectionfilter)
    * [select(value: mixed, append:bool)](#BitrixQueryBuilderSectionselect)
    * [buildTree()](#BitrixQueryBuilderSectionbuildTree)
    * [withItems(BitrixQueryBuilderElement = NULL)](#BitrixQueryBuilderSectionwithItems)
    * [execute()](#BitrixQueryBuilderSectionexecute)

### BitrixQueryBuilder
#### BitrixQueryBuilder.elements
Возвращает объект [BitrixQueryBuilderElement](#BitrixQueryBuilderElement)
```php
BitrixQueryBuilder::elements('furniture_products')
```
#### BitrixQueryBuilder.sections
Возвращает объект [BitrixQueryBuilderSection](#BitrixQueryBuilderSection)
```php
BitrixQueryBuilder::sections('furniture_products')
```

### BitrixQueryBuilderElement
#### BitrixQueryBuilderElement.order
#### BitrixQueryBuilderElement.limit
```php
$arItems = BitrixQueryBuilder::elements('furniture_products')
    ->order(['SORT => 'ASC'])
    ->limit(20)
    ->execute();
```
#### BitrixQueryBuilderElement.page
* page(pageNumber: int, limit: int)
    * pageNumber - номер страницы
    * limit - количество элементов на странице
    
```php
$arItems = BitrixQueryBuilder::elements('furniture_products')
    ->page(5, 10)
    ->execute();
```

#### BitrixQueryBuilderElement.select
```php
$arItems = BitrixQueryBuilder::elements('furniture_products')
    ->select([
        'NAME',
        'PREVIEW_TEXT'
    ])
    ->limit(20)
    ->execute();

//или

$arItems = BitrixQueryBuilder::elements('furniture_products')
    ->select('NAME', true)
    ->select('PREVIEW_TEXT', true)
    ->limit(20)
    ->execute();
```

#### BitrixQueryBuilderElement.properties
```php
$arItems = BitrixQueryBuilder::elements('furniture_products')
    ->select([
        'NAME',
        'PREVIEW_TEXT',
        'PROPERTY_COLOR'
    ])
    ->limit(20)
    ->execute();

//или

$arItems = BitrixQueryBuilder::elements('furniture_products')
    ->select([
        'NAME',
        'PREVIEW_TEXT'
    ])
    ->properties([
        'COLOR'
    ])
    ->limit(20)
    ->execute();
```

#### BitrixQueryBuilderElement.filter
```php
$arItems = BitrixQueryBuilder::elements('furniture_products')
    ->filter('ACTIVE', 'Y')
    ->filter('NAME', '%A%')
    ->filter('LOGIC', [
        "LOGIC" => "OR",
        ["<PROPERTY_RADIUS" => 50, "=PROPERTY_CONDITION" => "Y"],
        [">=PROPERTY_RADIUS" => 50, "!=PROPERTY_CONDITION" => "Y"],
    ])
    ->limit(20)
    ->execute();

//или

$arItems = BitrixQueryBuilder::elements('furniture_products')
    ->filter([
        'ACTIVE' => 'Y',
        'NAME' => '%table%',
        [
            "LOGIC" => "OR",
            ["<PROPERTY_RADIUS" => 50, "=PROPERTY_CONDITION" => "Y"],
            [">=PROPERTY_RADIUS" => 50, "!=PROPERTY_CONDITION" => "Y"],
        ]
    ])
    ->select('PREVIEW_TEXT', true)
    ->limit(20)
    ->execute();
```