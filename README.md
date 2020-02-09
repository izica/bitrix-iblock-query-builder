## Установка
Установить через composer(или скачать), подключить в php_interface/init.php
```
composer require izica/bitrix-iblock-query-builder
```

## Использование

### Возможности
* Получение разделов и элементов инфоблоков
* Стрелочные вызовы функций в любом порядке
* Мапинг результатов
* Автокеширование
* Автоматическое получение свойств элементов, с возможностью отключить получение свойств

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
    ->map(function($arItem){
        $arItem['PREVIEW_PICTURE'] = CFile::GetPath($arItem['PREVIEW_PICTURE']);
        return $arItem;
    })
    ->execute();

$arBadgesItems = IblockQuery::sections()
    ->filter($arFilter)
    ->select($arSelect)
    ->sort($arSort)
    ->nav($arNav)
    ->cache()
    ->execute();
```

#### Мапинг результатов и Автокеширование
Кеширование сработает уже после функции map(), поэтому запрос на картинки тоже сработает только 1 раз до кеширования.

`cache($module = 'iblock-query', $ttl = 3600)`

```php
$arBadgesSections = IblockQuery::items()
    ->filter($arFilter)
    ->select($arSelect)
    ->sort($arSort)
    ->nav($arNav)
    ->properties(false)
    ->cache()
    ->map(function($arItem){
        $arItem['PREVIEW_PICTURE'] = CFile::GetPath($arItem['PREVIEW_PICTURE']);
        return $arItem;
    })
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
