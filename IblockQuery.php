<?
require_once 'IblockQueryResult.php';

/**
 * Class IblockQuery
 */
class IblockQuery
{
    /**
     * @var bool
     */
    private $bCache = false;
    /**
     * @var string
     */
    private $sCacheModule = '';
    /**
     * @var int
     */
    private $nCacheTtl = 3600;
    /**
     * @var string
     */
    private $sCacheKey = '';
    /**
     * @var null
     */
    private $obCache = null;

    /**
     * @var bool
     */
    private $bProperties = true;
    /**
     * @var string
     */
    private $sType = '';
    /**
     * @var bool
     */
    private $arFilter = false;
    /**
     * @var array
     */
    private $arSort = ['SORT' => 'ASC'];
    /**
     * @var array
     */
    private $arNav = [
        "nPageSize" => 99999
    ];
    /**
     * @var array
     */
    private $arSelect = ['*'];
    /**
     * @var string
     */
    private $sListPageUrl = '';
    /**
     * @var string
     */
    private $sDetailPageUrl = '';
    /**
     * @var string
     */
    private $sSectionPageUrl = '';
    /**
     * @var callable
     */
    private $fnMap = null;

    /**
     * IblockQuery constructor.
     * @param string $type
     */
    public function __construct($type = 'items')
    {
        $this->sType = $type;
    }

    /**
     * @return IblockQuery
     */
    public static function items()
    {
        return new IblockQuery('items');
    }

    /**
     * @return IblockQuery
     */
    public static function sections()
    {
        return new IblockQuery('sections');
    }

    /**
     * @return $this
     */
    public function properties($enabled = true)
    {
        $this->bProperties = $enabled;
        return $this;
    }

    /**
     * @param $sort
     * @return $this
     */
    public function sort($sort)
    {
        $this->arSort = $sort;
        return $this;
    }

    /**
     * @param $filter
     * @return $this
     */
    public function filter($filter)
    {
        $this->arFilter = $filter;
        return $this;
    }

    /**
     * @param $nav
     * @return $this
     */
    public function nav($nav)
    {
        $this->arNav = $nav;
        return $this;
    }

    /**
     * @param string $module
     * @param int $ttl
     * @return $this
     */
    public function cache($module = 'iblock-query', $ttl = 3600)
    {
        $this->bCache = true;
        $this->sCacheModule = $module;
        $this->nCacheTtl = $ttl;

        return $this;
    }

    /**
     * @param $arSelect
     * @return $this
     */
    public function select($arSelect)
    {
        $this->arSelect = $arSelect;
        return $this;
    }

    /**
     * @param $sListPageUrl
     * @return $this
     */
    public function listPageUrl($sListPageUrl)
    {
        $this->sListPageUrl = $sListPageUrl;
        return $this;
    }

    /**
     * @param $sDetailPageUrl
     * @return $this
     */
    public function detailPageUrl($sDetailPageUrl)
    {
        $this->sDetailPageUrl = $sDetailPageUrl;
        return $this;
    }

    /**
     * @param $sSectionPageUrl
     * @return $this
     */
    public function sectionPageUrl($sSectionPageUrl)
    {
        $this->sSectionPageUrl = $sSectionPageUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    private function getCache()
    {
        $this->obCache = Bitrix\Main\Data\Cache::createInstance();
        $this->sCacheKey = md5(serialize([
            $this->arFilter,
            $this->arNav,
            $this->arSort,
            $this->arSelect,
            $this->sListPageUrl,
            $this->sDetailPageUrl,
            $this->sSectionPageUrl,
        ]));

        if ($this->obCache->initCache($this->nCacheTtl, $this->sCacheKey, $this->sCacheModule)) {
            return json_decode($this->obCache->getVars(), true);
        }

        return false;
    }

    /**
     * @return IblockQueryResult
     */
    private function getItems()
    {
        $arItems = [];

        $dbResult = CIBlockElement::GetList($this->arSort, $this->arFilter, false, $this->arNav, $this->arSelect);
        $dbResult->SetUrlTemplates($this->sDetailPageUrl, $this->sSectionPageUrl, $this->sListPageUrl);

        if ($this->bProperties) {
            while ($obItem = $dbResult->GetNextElement()) {
                $arFields = $obItem->GetFields();
                $arFields['PROPERTIES'] = $obItem->GetProperties();
                $arItems[] = $arFields;
            }
        } else {
            while ($obItem = $dbResult->GetNextElement()) {
                $arFields = $obItem->GetFields();
                $arItems[] = $arFields;
            }
        }

        if (is_callable($this->fnMap)) {
            $arItems = array_map($this->fnMap, $arItems);
        }

        return IblockQueryResult::fromDbResult($dbResult, $arItems);
    }

    /**
     * @return IblockQueryResult
     */
    private function getSections()
    {
        $arSections = [];

        $dbResult = CIBlockSection::GetList($this->arSort, $this->arFilter, false, $this->arSelect);
        $dbResult->SetUrlTemplates($this->sDetailPageUrl, $this->sSectionPageUrl, $this->sListPageUrl);
        while ($arSection = $dbResult->GetNext()) {
            $arSections[] = $arSection;
        }

        if (is_callable($this->fnMap)) {
            $arSections = array_map($this->fnMap, $arSections);
        }

        return IblockQueryResult::fromDbResult($dbResult, $arSections);
    }

    /**
     * @param callable $fnMap
     * @return $this
     */
    public function map($fnMap = null)
    {
        if (is_callable($fnMap)) {
            $this->fnMap = $fnMap;
        }

        return $this;
    }

    /**
     * @return array|mixed
     */
    public function execute()
    {
        $arResult = null;

        if ($this->arFilter === false) {
            echo 'IblockQuery->filter() required';
            return [];
        }

        if ($_GET['clear_cache'] == 'Y') {
            Bitrix\Main\Data\Cache::clearCache(true);
        }

        if ($this->bCache) {
            $arResult = $this->getCache();
            if ($arResult !== false) {
                return IblockQueryResult::fromJson($arResult);
            }
        }

        if ($this->sType == 'items') {
            $arResult = $this->getItems();
        }

        if ($this->sType == 'sections') {
            $arResult = $this->getSections();
        }

        if ($this->bCache) {
            $this->obCache->startDataCache();
            $this->obCache->endDataCache($arResult->toJson());
        }

        return $arResult;
    }
}
