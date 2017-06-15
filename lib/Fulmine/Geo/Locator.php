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
    private static $currentLocation = null;

    /** @var Providers\ILocationProvider $locationProvider */
    private static $locationProvider = null;

    /** @var Model\IModel $locationModel */
    private static $locationModel = null;

    public static function init(Providers\ILocationProvider $locationProvider, Model\IModel $model){
        static::$locationProvider = $locationProvider;
        static::$locationModel = $model;
    }

    public static function startupCheck(){
        /** @var \Fulmine\Geo\Location\ILocation $location */
        /** @var \Fulmine\Geo\Location\ILocation $locationUrl */
        if(is_object($location = static::getLocationFromSession()))
            if($location->isUrlValid())
                return;
            else{
                $locationUrl = static::getLocationByUrl();
                if($locationUrl->isGlobal()) {
                    list($redirectUrl, $bDoRedirect) = $location->getUrlByGlobalUrl(static::getCurrentFullUrl());

                    if($bDoRedirect)
                        LocalRedirect($redirectUrl);
                }
            }
    }

    public static function getLocation(){
        /** @var \Fulmine\Geo\Location\ILocation $location */

        if(is_object(static::$currentLocation))
            return static::$currentLocation;
        elseif(is_object($location = static::getLocationFromSession()) and $location->isUrlValid())
            return static::$currentLocation = $location;
        else
            return static::$currentLocation = static::getLocationByUrl();
    }

    public static function isLocationSelected(){
        return isset($_SESSION['fulmine']['Geo']['Location']);
    }

    private static function getCurrentModelUrl(){
        return $_SERVER['SERVER_NAME'];
    }

    private static function getCurrentFullUrl(){
        return \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getRequestUri();
    }

    private static function getLocationFromSession(){
        return isset($_SESSION['fulmine']['Geo']['Location']) ? unserialize($_SESSION['fulmine']['Geo']['Location']) : null;
    }
    static private function getLocationByUrl(){
        return static::$locationModel->getLocationByFilter(array(
            'URL' => static::getCurrentModelUrl()
        ));
    }

    static private function getLocationByIp(){
        return static::$locationModel->getLocationByFilter(static::$locationProvider->getLocationAsFilter());
    }
}