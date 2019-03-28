## Установка
Установить через composer(или скачать), подключить в php_interface/init.php
```
composer require izica/iblock-query
```

## Использование

#### Примечание
Массивы $arSort, $arFilter, $arSelect, $arNav соответсвуют формату [https://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/getlist.php]

```php
$arBadgesSections = IblockQuery::items()
    ->filter($arFilter)
    ->select($arSelect)
    ->sort($arSort)
    ->nav($arNav)
    ->properties(false) // выключает запрос на доп. свойства
    ->cache()
    ->execute();

$arBadgesItems = IblockQuery::sections()
    ->filter($arFilter)
    ->select($arSelect)
    ->sort($arSort)
    ->nav($arNav)
    ->cache()
    ->execute();
```



#### Получение элементов инфоблока
```php
$arBadgesItems = IblockQuery::items()
    ->filter(['IBLOCK_ID' => $nBadgesIblockId])
    ->select(['ID', 'NAME', 'PROPERTY_COLOR'])
    ->execute();
```

#### Получение разделов инфоблока
```php
$arBadgesSections = IblockQuery::sections()
    ->filter(['IBLOCK_ID' => $nBadgesIblockId])
    ->select(['ID', 'NAME', 'PROPERTY_COLOR'])
    ->execute();
```


#### Получение элементов без доп. свойств
```php
$arBadgesItems = IblockQuery::items()
    ->filter($arFilter)
    ->select($arSelect)
    ->sort($arSort)
    ->nav($arNav)
    ->properties(false)
    ->cache()
    ->execute();
```