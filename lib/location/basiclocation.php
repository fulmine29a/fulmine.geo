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
    protected $rawData;

    function __construct(array $rawData)
    {
        $this->rawData = $rawData;
/*
        $this->baseUrl = $this->$rawData['BASE_URL'];
        $this->globalLocation = $this->$rawData['GLOBAL'] == 'Y';
        $this->country = $this->$rawData['COUNTRY'];
        $this->region = $this->$rawData['REGION'];
        $this->city = $this->$rawData['CITY'];
*/
    }

    /**
     * @return bool
     */
    function isUrlValid()
    {
        return in_array($_SERVER['SERVER_NAME'], $this->rawData['BASE_URL']);
    }

    /**
     * @return bool
     */
    function isGlobal()
    {
        return false;
    }

    /**
     * @param string $url
     * @return array
     */
    function getUrlByGlobalUrl($url)
    {
        return array('http://'.$this->rawData['BASE_URL'][0].$url, true);
    }

    /**
     * @param array $fields
     * @return array
     */
    function getFields(array $fields)
    {
        return array_intersect_key($this->rawData, array_flip($fields));
    }
}