<?

/**
 * Class IblockQueryResult
 */
class IblockQueryResult
{
    /**
     * @var array
     */
    private $arItems = [];
    /**
     * @var array
     */
    private $arNav = [];
    /**
     * @var int
     */
    private $nCount = 0;

    /**
     * IblockQueryResult constructor.
     * @param $arItems
     * @param $arNav
     * @param $nCount
     */
    public function __construct($arItems, $arNav, $nCount)
    {
        $this->arItems = $arItems;
        $this->arNav = $arNav;
        $this->nCount = $nCount;
    }

    /**
     * @param $dbResult
     * @param $arItems
     * @return IblockQueryResult
     */
    public static function fromDbResult($dbResult, $arItems)
    {
        $arNav = [
            'PAGES'          => $dbResult->NavPageCount,
            'PAGE'           => $dbResult->NavPageNomer,
            'PAGE_SIZE'      => $dbResult->NavPageSize,
            'SELECTED_COUNT' => $dbResult->nSelectedCount,
            'NEXT_PAGE'      => (int)$dbResult->NavPageCount !== (int)$dbResult->NavPageNomer,
        ];
        $nCount = count($arItems);

        return new IblockQueryResult($arItems, $arNav, $nCount);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->arItems;
    }

    /**
     * @return array | null
     */
    public function first()
    {
        if (count($this->arItems) === 0) {
            return null;
        }
        return $this->arItems[0];
    }

    /**
     * @return array
     */
    public function nav()
    {
        return $this->arNav;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->nCount;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'ITEMS' => $this->arItems,
            'NAV'   => $this->arNav,
            'COUNT' => $this->nCount,
        ];
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @param $arData
     * @return IblockQueryResult
     */
    public static function fromArray($arData)
    {
        return new IblockQueryResult($arData['ITEMS'], $arData['NAV'], $arData['COUNT']);
    }
}
