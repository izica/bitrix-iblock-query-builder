<?php

class BitrixQueryBuilderElement {
    private $arOrder = ['SORT' => 'ASC'];
    private $arFilter = [];
    private $arGroupBy = false;
    private $arNavStartParams = [
        'nPageSize' => 500
    ];
    private $arSelect = ['*'];
    private $bWithProperties = false;

    function __construct($sIblockCode) {
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

    public function getIblockId() {
        return $this->arFilter['IBLOCK_ID'];
    }

    public function withProperties() {
        $this->bWithProperties = true;
        return $this;
    }

    public function select($anyValue, $bAppend = false) {
        if ($bAppend) {
            $this->arSelect[] = $anyValue;
        } else {
            $this->arSelect = $anyValue;
        }
        return $this;
    }

    public function filter($sFilterProperty, $sFilterValue = false) {
        if($sFilterValue == false){
            $this->arFilter = $sFilterValue;
        }else{
            $this->arFilter[$sFilterProperty] = $sFilterValue;
        }
        return $this;
    }

    public function order($arValue) {
        $this->arOrder = $arValue;
        return $this;
    }

    public function limit($nLimit) {
        $this->arNavStartParams['nPageSize'] = $nLimit;
        return $this;
    }

    public function execute() {
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
