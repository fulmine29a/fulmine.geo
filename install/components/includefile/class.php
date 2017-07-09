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

        if(empty($this->arParams['LOCATION'])
            and class_exists('\Fulmine\Geo\MainLocator')
        )
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
                            $this->arResult['FOUND_RECURSIVE'] = true;
                            $this->includeComponentTemplate();
                            break;
                        }
                    }
                    if($level == -1)
                        $this->AbortResultCache();
                }else
                    $this->AbortResultCache();

            }
        };

        if($GLOBALS['APPLICATION']->GetShowIncludeAreas())
            $this->prepareMenu();

        return $this->arResult['FILE_PATH'];
    }

    function getIncludeEntryId(){
        return 'INCLUDE';
    }

    protected function checkPath($fn){
        return $this->arResult['FILE_PATH'] = \Fulmine\Geo\IncludeFileController::findFileForLocation($fn, $this->location);
    }

    protected function getRelPath($abs){
        return substr($abs, strlen(\Bitrix\Main\Application::getDocumentRoot()));
    }

    protected function prepareMenu(){
        global $APPLICATION;
        $editor = '&site='.SITE_ID.'&back_url='.urlencode($_SERVER['REQUEST_URI']).'&templateID='.urlencode(SITE_TEMPLATE_ID);

        if(file_exists($absFilePath = $this->arResult['FILE_PATH'])) {
            $relFilePath = $this->getRelPath($absFilePath);

            $this->addEditAction(
                $this->getIncludeEntryId(),
                "/bitrix/admin/public_file_edit.php?lang=".LANGUAGE_ID."&from=includefile&path=".urlencode($relFilePath).$editor,
                'Редактировать этот файл'
            );

            $relDirectory = \Fulmine\Geo\IncludeFileController::getIncludeFileSection($absFilePath);
            $absPathForThisLocation = \Fulmine\Geo\IncludeFileController::getFileNameForThisLocation(
                $relDirectory.DIRECTORY_SEPARATOR.\Bitrix\Main\IO\Path::getName($this->arParams['FILE_NAME']),
                $this->location
            );

            if($absFilePath != $absPathForThisLocation){
                $this->addEditAction(
                    $this->getIncludeEntryId(),
                    "/bitrix/admin/public_file_edit.php?lang=".LANGUAGE_ID."&from=main.include&new=Y&path=".urlencode($this->getRelPath($absPathForThisLocation))."&new=Y&template=".urlencode($relFilePath).$editor,
                    'Создать для этой локации',
                    array(
                        "ICON" => "bx-context-toolbar-create-icon",
                        'ALT' => 'Создать новый файл для этой локации'
                    )
                );
            }else {
                $this->addDeleteAction(
                    $this->getIncludeEntryId(),
                    'javascript:' . $APPLICATION->GetPopupLink(array(
                        "URL" => "/bitrix/admin/public_file_delete.php?lang=" . LANGUAGE_ID . "&site=" . SITE_ID . '&back_url=' . urlencode($_SERVER['REQUEST_URI']) . "&path=" . urlencode($relFilePath),
                        "PARAMS" => Array(
                            "min_width" => 250,
                            "min_height" => 180,
                            'height' => 180,
                            'width' => 440
                        )
                    ))
                    ,
                    'Удалить для этой локации'
                );
            }
        }else{
            $relFilePath = $this->arParams['FILE_NAME'];

            $relDirectory = \Bitrix\Main\IO\Path::getDirectory($relFilePath);

            $this->addIncludeAreaIcon(array(
                "URL" => 'javascript:'.$APPLICATION->GetPopupLink(
                        array(
                            'URL' => "/bitrix/admin/public_file_edit.php?lang=".LANGUAGE_ID."&from=main.include&new=Y&path=".urlencode($this->getRelPath(\Fulmine\Geo\IncludeFileController::getFileNameForGlobal($relFilePath)))."&new=Y&template=blanck".$editor,
                            "PARAMS" => array(
                                'width' => 770,
                                'height' => 570,
                                'resize' => true
                            )
                        )
                    ),
                "DEFAULT" => $APPLICATION->GetPublicShowMode() != 'configure',
                "ICON" => "bx-context-toolbar-create-icon",
                "TITLE" => 'Создать ГЛОБАЛЬНЫЙ для '.$relDirectory,
                'ALT' => 'Создать ГЛОБАЛЬНЫЙ файл для ВСЕХ локации'
            ));
        }
    }
}