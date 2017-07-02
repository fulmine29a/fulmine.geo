<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class CIncludeFileComponent extends CBitrixComponent{
    /** @var  \Fulmine\Geo\Location\ILocation $location */
    public $location;

    public function onPrepareComponentParams($arParams)
    {
        $result = array(
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => isset($arParams["CACHE_TIME"]) ?$arParams["CACHE_TIME"]: 36000000,
            'FILE_NAME' => $arParams['FILE_NAME'],
            'RECURSIVE' => ($arParams['RECURSIVE'] == 'Y')
        );

        if(empty($this->arParams['LOCATION']))
            $this->location = \Fulmine\Geo\MainLocator::getLocation();
        else
            $this->location = $this->arParams['LOCATION'];

        return $result;
    }

    public function executeComponent()
    {
        if(empty($this->arParams['FILE_NAME']))
            return false;

        if(!(
            \Bitrix\Main\Loader::includeModule('fulmine.geo')
            and class_exists('\Fulmine\Geo\MainLocator')
            and \Fulmine\Geo\MainLocator::isInited())
        )
            return false;

        if($this->startResultCache($this->arParams['CACHE_TIME'],
            array(
                $this->arParams['FILE_NAME'],
                $this->location->getFileSuffixes()
            )
        ))
        {
            if($this->checkPath($this->arParams['FILE_NAME']))
                $this->includeComponentTemplate();
            elseif($this->arParams['RECURSIVE']){
                $pi = pathinfo($this->arParams['FILE_NAME']);

                $dirs = explode(DIRECTORY_SEPARATOR, $pi['dirname']);

                array_shift($dirs);

                if($level = count($dirs)){
                    while($level != -1){
                        $level--;
                        array_pop($dirs);

                        $dir = DIRECTORY_SEPARATOR;
                        foreach ($dirs as $oneDir)
                            $dir.= $oneDir . DIRECTORY_SEPARATOR;

                        if($this->checkPath($dir.$pi['basename'])) {
                            $this->includeComponentTemplate();
                            return $this->arResult['FILE_PATH'];
                        }
                    }

                    $this->AbortResultCache();
                }else
                    $this->AbortResultCache();

            }
        };
        return $this->arResult['FILE_PATH'];
    }

    protected function checkPath($fn){
        return $this->arResult['FILE_PATH'] = \Fulmine\Geo\IncludeFileController::findFileForLocation($fn, $this->location);
    }
}