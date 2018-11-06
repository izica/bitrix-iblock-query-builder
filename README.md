## Установка
Установить через composer(или скачать), подключить в php_interface/init.php
```
composer require izica/bitrix-query-builder
```

## Использование

##### Получение элементов инфоблока
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
##### Получение разделов инфоблока
```php
$arSections = BitrixQueryBuilder::sections('furniture_products')
    ->withItems()
    ->buildTree()
    ->execute();
```

## Документация
* [BitrixQueryBuilder](#BitrixQueryBuilder)
    * [elements(iblockCode: string)](#BitrixQueryBuilder.elements)
    * [sections(iblockCode: string)](#BitrixQueryBuilder.sections)
* [BitrixQueryBuilderElement](#BitrixQueryBuilderElement)
    * [order(value: array)](#BitrixQueryBuilderElement.order)
    * [limit(value: int)](#BitrixQueryBuilderElement.limit)
    * [page(page: int, perpage: int)](#BitrixQueryBuilderElement.page)
    * [filter(key: string, value: mixed)](#BitrixQueryBuilderElement.filter)
    * [filter(value: array)](#BitrixQueryBuilderElement.filter)
    * [select(value: mixed, append:bool)](#BitrixQueryBuilderElement.select)
    * [execute()](#BitrixQueryBuilderElement.exectute)
* [BitrixQueryBuilderSection](#BitrixQueryBuilderSection)
    * [order(value: array)](#BitrixQueryBuilderSection)
    * [limit(value: int)](#BitrixQueryBuilderSection.limit)
    * [page(page: int, perpage: int)](#BitrixQueryBuilderSection.page)
    * [filter(key: string, value: mixed)](#BitrixQueryBuilderSection.filter)
    * [filter(value: array)](#BitrixQueryBuilderSection.filter)
    * [select(value: mixed, append:bool)](#BitrixQueryBuilderSection.select)
    * [buildTree()](#BitrixQueryBuilderSection.buildTree)
    * [withItems(BitrixQueryBuilderElement = NULL)](#BitrixQueryBuilderSection.withItems)
    * [execute()](#BitrixQueryBuilderSection.execute)


### BitrixQueryBuilder
##### BitrixQueryBuilder.order
```php
$arItems = BitrixQueryBuilder::elements('furniture_products')
    ->order(['SORT => 'ASC'])
    ->execute();
```

