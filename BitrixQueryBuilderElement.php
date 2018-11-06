<?php

/**
 * Class BitrixQueryBuilderElement
 */
class BitrixQueryBuilderElement {
    /**
     * @var int
     */
    private $nIblockId = false;
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
    private $arGroupBy = false;
    /**
     * @var array
     */
    private $arNavStartParams = [
        'nPageSize' => 9999
    ];
    /**
     * @var array
     */
    private $arSelect = ['*'];

    /**
     * @var array
     */
    private $arProperties = [];

    /**
     * @var bool
     */
    private $bWithProperties = false;

    /**
     * BitrixQueryBuilderElement constructor.
     * @param string $sIblockCode
     * @throws Exception
     */
    function __construct($sIblockCode) {
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
     * @return mixed
     */
    public function getIblockId() {
        return $this->nIblockId;
    }

    /**
     * @return $this
     */
    public function properties($arProperties = []) {
        if (is_array($arProperties)) {
            $this->arProperties = $arProperties;
        }
        if (count($arProperties) > 0) {
            $this->bWithProperties = true;
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
     * @param string $sFilterPropertyKey
     * @param mixed $sFilterValue
     * @return $this
     */
    public function filter($sFilterPropertyKey, $sFilterValue = false) {
        if ($sFilterPropertyKey == 'LOGIC' && $sFilterValue !== false) {
            $this->arFilter[] = $sFilterValue;
        } else {
            if ($sFilterValue == false) {
                $this->arFilter = $sFilterValue;
            } else {
                $this->arFilter[$sFilterPropertyKey] = $sFilterValue;
            }
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
     * @param int $nLimit
     * @return $this
     */
    public function limit($nLimit) {
        $this->arNavStartParams['nPageSize'] = $nLimit;
        return $this;
    }

    /**
     * @param int $nPage
     * @param int $nLimit
     * @return $this
     */
    public function page($nPage, $nLimit = 20) {
        $this->arNavStartParams['iNumPage'] = $nPage;
        $this->arNavStartParams['nPageSize'] = $nLimit;
        return $this;
    }

    /**
     * @return array
     */
    public function execute() {
        if ($this->bWithProperties) {
            foreach ($this->arProperties as $sProperty) {
                $this->arSelect[] = $sProperty;
            }
        }
        $this->arFilter['IBLOCK_ID'] = $this->nIblockId;
        $arItems = [];
        $resObject = CIBlockElement::GetList($this->arOrder, $this->arFilter, $this->arGroupBy, $this->arNavStartParams, $this->arSelect);
        while ($obItem = $resObject->GetNextElement()) {
            $arItem = $obItem->GetFields();
            if ($this->bWithProperties) {
                $arItem['PROPERTIES'] = $obItem->GetProperties();
            }
            $arItems[] = $arItem;
        }
        return $arItems;
    }
}
