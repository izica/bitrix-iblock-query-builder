<?php

/**
 * Class BitrixQueryBuilderSection
 */
class BitrixQueryBuilderSection {
    /**
     * @var int
     */
    private $nIblockId = false;
    /**
     * @var string
     */
    private $sIblockCode = '';
    /**
     * @var array
     */
    private $arOrder = ['SORT' => 'ASC'];
    /**
     * @var array
     */
    private $arFilter = [];
    /**
     * @var bool
     */
    private $bIncCnt = false;
    /**
     * @var array
     */
    private $arSelect = ['*'];
    /**
     * @var bool
     */
    private $bBuildTree = false;
    /**
     * @var bool
     */
    private $bWithItems = false;
    /**
     * @var array
     */
    private $arItems = [];
    /**
     * @var array
     */
    private $arNavStartParams = [
        'nPageSize' => 9999
    ];

    /**
     * BitrixQueryBuilderSection constructor.
     * @param string $sIblockCode
     * @throws Exception
     */
    function __construct($sIblockCode) {
        $this->sIblockCode = $sIblockCode;
        CModule::IncludeModule('iblock');
        $this->nIblockId = $this->getIblockIdByCode($sIblockCode);
        return $this;
    }

    /**
     * @param string $sIblockCode
     * @return mixed
     * @throws Exception
     */
    private function getIblockIdByCode($sIblockCode) {
        $obResult = CIBlock::GetList([], ['SITE_ID' => 's1', "CODE" => $sIblockCode], true);
        if ($arResult = $obResult->Fetch()) {
            return $arResult['ID'];
        } else {
            throw new Exception("Iblock " . $sIblockCode . " not found");
        }
    }

    /**
     * @return $this
     */
    public function buildTree() {
        $this->bBuildTree = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function withCount() {
        $this->bIncCnt = true;
        return $this;
    }

    /**
     * @param int $nLimit
     * @return $this
     */
    public function limit($nLimit) {
        $this->arNavStartParams['nPageSize'] = $nLimit;
        return $this;
    }

    /**
     * @param $nPage
     * @param int $nLimit
     * @return $this
     */
    public function page($nPage, $nLimit = 20) {
        $this->arNavStartParams['iNumPage'] = $nPage;
        $this->arNavStartParams['nPageSize'] = $nLimit;
        return $this;
    }

    /**
     * @param BitrixQueryBuilderElement|NULL $obElementBuilder
     * @return $this
     * @throws Exception
     */
    public function withItems(BitrixQueryBuilderElement $obElementBuilder = NULL) {
        if ($obElementBuilder === NULL) {
            $obElementBuilder = new BitrixQueryBuilderElement($this->sIblockCode);
            $obElementBuilder->limit(9999);
        }
        $this->bWithItems = true;
        if ($this->nIblockId != $obElementBuilder->getIblockId()) {
            global $APPLICATION;
            $APPLICATION->ThrowException("withItems: Iblocks codes doesn't match");
        }
        $arItems = $obElementBuilder->select('IBLOCK_SECTION_ID', true)->execute();
        foreach ($arItems as $arItem) {
            if ($arItem['IBLOCK_SECTION_ID'] !== NULL) {
                $this->arItems[$arItem['IBLOCK_SECTION_ID']] = $arItem;
            }
        }
        return $this;
    }

    /**
     * @param mixed $anyValue
     * @param bool $bAppend
     * @return $this
     */
    public function select($anyValue, $bAppend = false) {
        if ($bAppend) {
            $this->arSelect[] = $anyValue;
        } else {
            $this->arSelect = $anyValue;
        }
        return $this;
    }

    /**
     * @param mixed $sFilterProperty
     * @param bool $sFilterValue
     * @return $this
     */
    public function filter($sFilterProperty, $sFilterValue = false) {
        if($sFilterValue == false){
            $this->arFilter = $sFilterValue;
        }else{
            $this->arFilter[$sFilterProperty] = $sFilterValue;
        }
        return $this;
    }

    /**
     * @param array $arValue
     * @return $this
     */
    public function order($arValue) {
        $this->arOrder = $arValue;
        return $this;
    }

    /**
     * @return array
     */
    public function execute() {
        $this->arFilter['IBLOCK_ID'] = $this->nIblockId;

        if ($this->arSelect[0] != '*') {
            if ($this->bBuildTree) {
                $this->arSelect[] = 'IBLOCK_SECTION_ID';
            }
            $this->arSelect[] = 'ID';
        }

        $arSections = [];
        $resObject = CIBlockSection::GetList($this->arOrder, $this->arFilter, $this->bIncCnt, $this->arSelect, $this->arNavStartParams);
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

    /**
     * @param $arSections
     * @return array
     */
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

    /**
     * @param $arRootSection
     * @param $arChildSections
     */
    private function _appendChildSections(&$arRootSection, $arChildSections) {
        if (isset($arChildSections[$arRootSection['ID']])) {
            $arRootSection['SECTIONS'] = $arChildSections[$arRootSection['ID']];
            foreach ($arRootSection['SECTIONS'] as &$arSection) {
                $this->_appendChildSections($arSection, $arChildSections);
            }
        }
    }
}
