<?php

class BitrixQueryBuilderSection {
    private $sIblockCode = '';
    private $arOrder = ['SORT' => 'ASC'];
    private $arFilter = [];
    private $bIncCnt = false;
    private $arSelect = ['*'];
    private $bBuildTree = false;
    private $bWithItems = false;
    private $arItems = [];

    function __construct($sIblockCode) {
        $this->sIblockCode = $sIblockCode;
        CModule::IncludeModule('iblock');
        $this->arFilter['IBLOCK_ID'] = $this->getIblockIdByCode($sIblockCode);
        return $this;
    }

    private function getIblockIdByCode($sIblockCode) {
        $obResult = CIBlock::GetList([], ['SITE_ID' => 's1', "CODE" => $sIblockCode], true);
        if ($arResult = $obResult->Fetch()) {
            return $arResult['ID'];
        } else {
            throw new Exception("Iblock " . $sIblockCode . " not found");
        }
    }

    public function buildTree() {
        $this->bBuildTree = true;
        return $this;
    }

    public function withCount() {
        $this->bIncCnt = true;
        return $this;
    }

    public function withItems(BitrixQueryBuilderElement $obElementBuilder = NULL) {
        if ($obElementBuilder === NULL) {
            $obElementBuilder = new BitrixQueryBuilderElement($this->sIblockCode);
        }
        $this->bWithItems = true;
        if ($this->arFilter['IBLOCK_ID'] != $obElementBuilder->getIblockId()) {
            global $APPLICATION;
            $APPLICATION->ThrowException("withItems: Iblocks codes doesn't match");
        }
        $arItems = $obElementBuilder->select('IBLOCK_SECTION_ID', true)->execute();
        foreach ($arItems as $arItem) {
            if ($arItem['IBLOCK_SECTION_ID'] !== NULL) {
                $this->arItems[$arItem['IBLOCK_SECTION_ID']] = $arItem;
            }
        }
        pre($this->arItems);
        return $this;
    }

    public function select($arValue) {
        $this->arSelect = $arValue;
        return $this;
    }

    public function filter($sFilterProperty, $sFilterValue) {
        $this->arFilter[$sFilterProperty] = $sFilterValue;
        return $this;
    }

    public function order($arValue) {
        $this->arOrder = $arValue;
        return $this;
    }

    public function execute() {
        if ($this->arSelect[0] != '*') {
            if ($this->bBuildTree) {
                $this->arSelect[] = 'IBLOCK_SECTION_ID';
            }
            $this->arSelect[] = 'ID';
        }

        $arSections = [];
        $resObject = CIBlockSection::GetList($this->arOrder, $this->arFilter, $this->bIncCnt, $this->arSelect);
        while ($arSection = $resObject->GetNext()) {
            if ($this->bBuildTree) {
                $arSection['SECTIONS'] = [];
            }
            if ($this->bWithItems) {
                if (isset($this->arItems[$arSection['ID']])) {
                    $arSection['ITEMS'][] = $this->arItems[$arSection['ID']];
                } else {
                    $arSection['ITEMS'] = [];
                }
            }
            $arSections[] = $arSection;
        }
        if ($this->bBuildTree) {
            return $this->_buildTree($arSections);
        }
        return $arSections;
    }

    private function _buildTree($arSections) {
        $arRootSections = [];
        $arChildSections = [];
        foreach ($arSections as $arSection) {
            if ($arSection['IBLOCK_SECTION_ID'] == NULL) {
                $arRootSections[] = $arSection;
            } else {
                $arChildSections[$arSection['IBLOCK_SECTION_ID']][] = $arSection;
            }
        }
        foreach ($arRootSections as &$arSection) {
            $this->_appendChildSections($arSection, $arChildSections);
        }
        return $arRootSections;
    }

    private function _appendChildSections(&$arRootSection, $arChildSections) {
        if (isset($arChildSections[$arRootSection['ID']])) {
            $arRootSection['SECTIONS'] = $arChildSections[$arRootSection['ID']];
            foreach ($arRootSection['SECTIONS'] as &$arSection) {
                $this->_appendChildSections($arSection, $arChildSections);
            }
        }
    }
}
