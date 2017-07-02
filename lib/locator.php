<?php
/**
 * Created by PhpStorm.
 * User: fulmine
 * Date: 15.06.2017
 * Time: 20:29
 */

namespace Fulmine\Geo;


class Locator
{
    const SELECT_LOCATION_GET_VAR = 'SELECT_LOCATION';

    /** @var Location\ILocation $currentLocation */
    protected static $currentLocation = null;

    /** @var Providers\ILocationProvider $locationProvider */
    protected static $locationProvider = null;

    /** @var Model\IModel $locationModel */
    protected static $locationModel = null;

    protected static $inited = false;

    public static function init(Providers\ILocationProvider $locationProvider, Model\IModel $model){
        static::$locationProvider = $locationProvider;
        static::$locationModel = $model;
        static::$inited = true;
    }

    public static function isInited(){
        return static::$inited;
    }

    public static function startupCheck(){
        /** @var \Fulmine\Geo\Location\ILocation $location */
        /** @var \Fulmine\Geo\Location\ILocation $locationUrl */

        if(static::forceSelectLocation())
            return;

        if(is_object($location = static::getLocationFromSession())) { // если локация в сессии

            if ($location->isUrlValid())
                // и урл подходит локации - то всё ок
                return;
            elseif(static::canRedirect()){ // урл локации не подходит и мы не на особом запросе
                $locationUrl = static::getLocationByUrl();

                if ($locationUrl->isGlobal()) {
                    // на глобальном урле переходим на урл сохраненой локации
                    static::redirectToLocation($location);
                    return;
                }else {
                    // если урл не глобальный - меняем локацию
                    static::setLocation($locationUrl);
                    static::setIsAutoSelectedLocation(true);
                }

            }

        }else{
            // если локация не в сесии

            $locationUrl = static::getLocationByUrl();

            if(is_object($locationUrl) and $locationUrl->isGlobal() and static::canRedirect()) {

                // если мы на глобальном урле и можем редиректиться
                if(is_object($location = static::getLocationByIp()) and (!$location->isGlobal())) {
                    // редиректимся если локация по айпи не глобальная, и не принудительный выбор глобальной
                    static::setLocation($location);
                    static::setIsAutoSelectedLocation();

                    static::redirectToLocation($location);
                }

            }

            if(static::canRedirect()){
                // если запрос не специальный, и мы остались в этой же локации - ставим эту локацию
                static::setLocation($locationUrl);
                static::setIsAutoSelectedLocation();
            }

        }
    }

    public static function getLocation(){
        /** @var \Fulmine\Geo\Location\ILocation $location */

        if(is_object(static::$currentLocation))
            return static::$currentLocation;
        elseif(is_object($location = static::getLocationFromSession()))
            return static::$currentLocation = $location;
        else
            return static::$currentLocation = static::getLocationByUrl();
    }
    public static function setLocation($loc){
        static::$currentLocation = $loc;
        $_SESSION['fulmine']['Geo']['Location'] = serialize($loc);
    }
    public static function clearLocation()
    {
        static::$currentLocation = $_SESSION['fulmine']['Geo']['Location'] = null;
    }
    public static function isLocationSelected(){
        return isset($_SESSION['fulmine']['Geo']['Location']);
    }
    public static function IsAutoSelectedLocation(){
        return $_SESSION['fulmine']['Geo']['autoLocation'];
    }
    public static function setIsAutoSelectedLocation($bAutoselected = true){
        $_SESSION['fulmine']['Geo']['autoLocation'] = $bAutoselected;
    }

    /**
     * @return Model\IModel
     */
    public static function getLocationModel()
    {
        return static::$locationModel;
    }

    public static function getSwitchLocationUrl(Location\ILocation $location){
        list($redirectUrl, $bDoRedirect) = $location->getUrlByGlobalUrl(static::getCurrentFullUrl());

        $url = new \Bitrix\Main\Web\Uri($redirectUrl);
        $url->addParams(array(static::SELECT_LOCATION_GET_VAR => $location->getId()));

        return $url->getUri();
    }


    protected static function redirectToLocation(Location\ILocation $location){

        if(static::canRedirect()) {
            list($redirectUrl, $bDoRedirect) = $location->getUrlByGlobalUrl(static::getCurrentFullUrl());

            if ($bDoRedirect)
                LocalRedirect($redirectUrl);
        }

    }

    protected static function forceSelectLocation(){
        $req = \Bitrix\Main\Context::getCurrent()->getRequest();

        if(isset($req[static::SELECT_LOCATION_GET_VAR])){
            if(
                is_object(
                    $location = static::$locationModel->getById((int)$req['SELECT_LOCATION'])
                )
            ){

                static::setLocation($location);
                static::setIsAutoSelectedLocation(false);

                if($location->isUrlValid()) {
                    $url = new \Bitrix\Main\Web\Uri($req->getDecodedUri());
                    $url->deleteParams(array(static::SELECT_LOCATION_GET_VAR));

                    LocalRedirect($url->getUri());
                }else
                    static::redirectToLocation($location);

                return true;
            };
        }

        return false;
    }
    protected static function canRedirect(){
        $req = \Bitrix\Main\Context::getCurrent()->getRequest();
        return
            ($req->getRequestMethod() == 'GET')
            && !$req->isAdminSection()
            && !$req->isAjaxRequest();
    }
    protected static function getCurrentModelUrl(){
        return $_SERVER['SERVER_NAME'];
    }

    protected static function getCurrentFullUrl(){ // TODO: сделать действительно full url

        $url = new \Bitrix\Main\Web\Uri(\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getRequestUri());
        $url->deleteParams(array(static::SELECT_LOCATION_GET_VAR));

        return $url->getUri();
    }

    protected static function getLocationFromSession(){
        return isset($_SESSION['fulmine']['Geo']['Location']) ? unserialize($_SESSION['fulmine']['Geo']['Location']) : null;
    }


    protected static function getGlobalLocation(){
        return static::$locationModel->getLocationByFilter(array(
            'GLOBAL' => 'Y'
        ));
    }



    protected static function getLocationByUrl(){
        return static::$locationModel->getLocationByFilter(array(
            'BASE_URL' => static::getCurrentModelUrl()
        ));
    }

    protected static function getLocationByIp(){
        if(!isset($_SESSION['fulmine']['Geo']['LocationIp'])) {
            $location = static::$locationModel->getLocationByFilter(static::$locationProvider->getLocationAsFilter());
            $_SESSION['fulmine']['Geo']['LocationIp'] = serialize($location);

            return $location;
        }else
            return unserialize($_SESSION['fulmine']['Geo']['LocationIp']);
    }
}