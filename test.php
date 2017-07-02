<?//
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//$APPLICATION->SetTitle("");
//
//$GLOBALS['USER']->isAdmin() or die();
//?><!--<pre>--><?//
//
//\Bitrix\Main\Loader::includeModule('fulmine.geo');
//
////class_alias('\\Fulmine\Geo\\Locator','\Fulmine\Geo\MainLocator');
//
////\Fulmine\Geo\Locator::clearLocation();
////unset($_SESSION['fulmine']['Geo']);
//
////\Fulmine\Geo\MainLocator::init(
////        new \Fulmine\Geo\Providers\SXGeoProvider(),
////        new \Fulmine\Geo\Model\CIBlock(
////                new \Fulmine\Geo\Location\StdFabric(),
////                'region_info'
////        )
////);
//
//print_r((new \Fulmine\Geo\Providers\SXGeoProvider())->getLocationAsFilter());
//
//
////\Fulmine\Geo\MainLocator::startupCheck();
//
//print_r($location = \Fulmine\Geo\MainLocator::getLocation());
//var_dump($_SESSION['fulmine']['Geo']);
//?><!--Global --><?//var_dump($location->isGlobal());
//?><!--Url Valid --><?//var_dump($location->isUrlValid());
//
//$parser = new \Fulmine\Geo\Parser($location, array(
//    'BASE_URL',
//    'REGION'
//));
//
//$parser->registerAsEndBuffer();
//
//$parser = new \Fulmine\Geo\Parser($location, array(
//    'BASE_URL',
//    'REGION'
//));
//
//$testStr = <<<EOL
//проверочный контент, урл #BASE_URL#, короткий регион #REGION#
//EOL;
//
//?>
<!--    </pre>-->
<!--    <textarea name="" id="" cols="30" rows="10">--><?//=$parser->replaceText($testStr)?><!--</textarea>-->
<!--    <div>-->
<!--        проверка onEndBufferContent #BASE_URL#  #IN_CITY_NAME#-->
<!--    </div>-->
<!---->
<!--    <pre>-->
<?////print_r(\Fulmine\Geo\IncludeFileController::getFileNameForThisLocation('/catalog/index.php', $location));?>
<!---->
<?////var_dump(\Fulmine\Geo\IncludeFileController::findFileForLocation('/test.php', $location))?>
<!---->
<?//var_dump($APPLICATION->IncludeComponent(
//    'fulmine.geo:includefile',
//    '',
//    array(
//        'FILE_NAME' => '/catalog/ddd/test.php',
//        'RECURSIVE' => 'Y'
//    )
//)
//)?>
<!--</pre>-->
<?// require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
