<?php
/**
 * Created by PhpStorm.
 * User: fulmine
 * Date: 15.06.2017
 * Time: 20:55
 */

namespace Fulmine\Geo\Location;


interface ILocation
{
    /**
     * @return bool
     */
    function isUrlValid();

    /**
     * @return bool
     */
    function isGlobal();

    /**
     * @param string $url
     * @return array
     */
    function getUrlByGlobalUrl($url);

    /**
     * @param array $fields
     * @return array
     */
    function getFields(array $fields);
}