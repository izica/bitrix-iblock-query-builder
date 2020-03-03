<?

/**
 * Class IblockQueryResult
 */
class IblockQueryResult
{
    private $arItems = [];
    private $arNav = [];
    private $nCount = 0;

    public function __construct($arItems, $arNav, $nCount)
    {
        $this->arItems = $arItems;
        $this->arNav = $arNav;
        $this->nCount = $nCount;
    }

    public static function fromDbResult($dbResult, $arItems)
    {
        $arNav = [
            'PAGES'          => $dbResult->NavPageCount,
            'PAGE'           => $dbResult->NavPageNomer,
            'PAGE_SIZE'      => $dbResult->NavPageSize,
            'SELECTED_COUNT' => $dbResult->nSelectedCount,
            'NEXT_PAGE'      => (int)$dbResult->NavPageCount !== (int)$dbResult->NavPageNomer
        ];
        $nCount = count($arItems);

        return new IblockQueryResult($arItems, $arNav, $nCount);
    }

    public function all()
    {
        return $this->arItems;
    }

    public function nav()
    {
        return $this->arNav;
    }

    public function count()
    {
        return $this->nCount;
    }

    public function toArray()
    {
        return [
            'ITEMS' => $this->arItems,
            'NAV'   => $this->arNav,
            'COUNT' => $this->nCount,
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public static function fromJson($sJson)
    {
        $arData = json_decode($sJson, true);
        return new IblockQueryResult($arData['ITEMS'], $arData['NAV'], $arData['COUNT']);
    }
}
