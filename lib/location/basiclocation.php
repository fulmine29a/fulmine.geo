<?php
/**
 * Created by PhpStorm.
 * User: fulmine
 * Date: 24.06.2017
 * Time: 20:29
 */

namespace Fulmine\Geo\Location;


class BasicLocation implements ILocation
{
    protected $baseUrl;
    protected $globalLocation;
    protected $country;
    protected $region;
    protected $city;

    function __construct(array $rawData)
    {
        $this->baseUrl = $rawData['BASE_URL'];
        $this->globalLocation = $rawData['GLOBAL'] == 'Y';
        $this->country = $rawData['COUNTRY'];
        $this->region = $rawData['REGION'];
        $this->city = $rawData['CITY'];
    }

    /**
     * @return bool
     */
    function isUrlValid()
    {
        return $_SERVER['SERVER_NAME'] == $this->baseUrl;
    }

    /**
     * @return bool
     */
    function isGlobal()
    {
        return $this->globalLocation;
    }

    /**
     * @param string $url
     * @return array
     */
    function getUrlByGlobalUrl($url)
    {
//        TODO: implementation
        return array('http://'.$this->baseUrl, true);
    }
}