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
    /** @var Location\ILocation $currentLocation */
    protected static $currentLocation = null;

    /** @var Providers\ILocationProvider $locationProvider */
    protected static $locationProvider = null;

    /** @var Model\IModel $locationModel */
    protected static $locationModel = null;

    public static function init(Providers\ILocationProvider $locationProvider, Model\IModel $model){
        static::$locationProvider = $locationProvider;
        static::$locationModel = $model;
    }
    public static function startupCheck(){
        /** @var \Fulmine\Geo\Location\ILocation $location */
        /** @var \Fulmine\Geo\Location\ILocation $locationUrl */
        if(is_object($location = static::getLocationFromSession())) {
            if ($location->isUrlValid())
                return;
            else {
                $locationUrl = static::getLocationByUrl();
                if ($locationUrl->isGlobal()) {
                    list($redirectUrl, $bDoRedirect) = $location->getUrlByGlobalUrl(static::getCurrentFullUrl());

                    if ($bDoRedirect)
                        LocalRedirect($redirectUrl);
                }
            }
        }else{
            if(is_object($locationUrl = static::getLocationByUrl()) and $locationUrl->isGlobal()) {
                if (is_object($location = static::getLocationByIp()) and (!$location->isGlobal())) {

                    static::setLocation($location);
                    static::setIsAutoSelectedLocation();


                    list($redirectUrl, $bDoRedirect) = $location->getUrlByGlobalUrl(static::getCurrentFullUrl());

                    if ($bDoRedirect)
                        LocalRedirect($redirectUrl);
                }
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
        return $_SESSION['fulmine']['Geo']['Location'];
    }
    public static function setIsAutoSelectedLocation($bAutoselected = true){
        $_SESSION['fulmine']['Geo']['Location'] = $bAutoselected;
    }


    protected static function getCurrentModelUrl(){
        return $_SERVER['SERVER_NAME'];
    }

    protected static function getCurrentFullUrl(){
        return \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getRequestUri();
    }

    protected static function getLocationFromSession(){
        return isset($_SESSION['fulmine']['Geo']['Location']) ? unserialize($_SESSION['fulmine']['Geo']['Location']) : null;
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