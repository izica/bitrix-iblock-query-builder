<?php
require 'BitrixQueryBuilderElement.php';
require 'BitrixQueryBuilderSection.php';

class BitrixQueryBuilder {
    public static function elements($sIblockCode) {
        return new BitrixQueryBuilderElement($sIblockCode);
    }

    public static function sections($sIblockCode) {
        return new BitrixQueryBuilderSection($sIblockCode);
    }
}
