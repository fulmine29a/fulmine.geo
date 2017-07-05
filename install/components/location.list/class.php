<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class CLocationListComponent extends CBitrixComponent{
    public $locationList;

    public function onPrepareComponentParams($arParams)
    {
        $result = array(
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => isset($arParams["CACHE_TIME"]) ?$arParams["CACHE_TIME"]: 36000000,
            "FIELDS" => isset($arParams['FIELDS']) ? : array('CITY_NAME'),
            'ADD_URL' => ($arParams['ADD_URL'] == 'Y') or !isset($arParams['ADD_URL']),
            'SORT' => is_array($arParams['SORT']) ? $arParams['SORT'] : false
        );
        return $result;
    }

    public function executeComponent()
    {
        if(!(
            \Bitrix\Main\Loader::includeModule('fulmine.geo')
            and class_exists('\Fulmine\Geo\MainLocator')
            and \Fulmine\Geo\MainLocator::isInited())
        )
            return false;

        if($this->startResultCache($this->arParams['CACHE_TIME'], \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getRequestUri()))
        {
            $this->getLocations();

            foreach ($this->locationList as $location){
                $this->arResult['ITEMS'][] = $this->prepareLocation($location);
            };

            $this->includeComponentTemplate();
        }
        return $this->arResult;
    }

    protected function getLocations(){
        $this->locationList = \Fulmine\Geo\MainLocator::getLocationModel()->getList(
            array(
                'sort' => $this->arParams['SORT']
            )
        );
    }
    protected function prepareLocation(\Fulmine\Geo\Location\ILocation $location){
        $item = $location->getFields($this->arParams['FIELDS']);

        $item['ID'] = $location->getId();

        if($this->arParams['ADD_URL'])
            $item['URL'] = \Fulmine\Geo\MainLocator::getSwitchLocationUrl($location);

        return $item;
    }
}