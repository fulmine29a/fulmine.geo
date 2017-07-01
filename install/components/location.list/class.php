<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class CLocationList extends CBitrixComponent{
    public $locationList;

    public function onPrepareComponentParams($arParams)
    {
        $result = array(
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => isset($arParams["CACHE_TIME"]) ?$arParams["CACHE_TIME"]: 36000000,
            "FIELDS" => isset($arParams['FIELDS']) ? : array('CITY_NAME'),
            'ADD_URL' => ($arParams['ADD_URL'] == 'Y') or !isset($arParams['ADD_URL'])
        );
        return $result;
    }

    public function executeComponent()
    {
        if($this->startResultCache($this->arParams['CACHE_TIME'], \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getRequestUri()))
        {
            $this->getLocations();

            foreach ($this->locationList as $location){
                $item = $location->getFields($this->arParams['FIELDS']);

                $item['ID'] = $location->getId();

                if($this->arParams['ADD_URL'])
                    $item['URL'] = \Fulmine\Geo\MainLocator::getSwitchLocationUrl($location);

                $this->arResult['ITEMS'][] = $item;
            };

            $this->includeComponentTemplate();
        }
        return;
    }

    protected function getLocations(){
        $this->locationList = \Fulmine\Geo\MainLocator::getLocationModel()->getList();
    }
}