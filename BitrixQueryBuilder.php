<?php
require 'BitrixQueryBuilderElement.php';
require 'BitrixQueryBuilderSection.php';

/**
 * Class BitrixQueryBuilder
 */
class BitrixQueryBuilder {
    /**
     * @param string $sIblockCode
     * @return BitrixQueryBuilderElement
     * @throws Exception
     */
    public static function elements($sIblockCode) {
        return new BitrixQueryBuilderElement($sIblockCode);
    }

    /**
     * @param string $sIblockCode
     * @return BitrixQueryBuilderSection
     * @throws Exception
     */
    public static function sections($sIblockCode) {
        return new BitrixQueryBuilderSection($sIblockCode);
    }
}
