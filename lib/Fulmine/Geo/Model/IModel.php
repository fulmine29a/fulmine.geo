<?php
/**
 * Created by PhpStorm.
 * User: fulmine
 * Date: 15.06.2017
 * Time: 21:37
 */

namespace Fulmine\Geo\Model;


interface IModel
{
    /**
     * @param array $filter
     * @return \Fulmine\Geo\Location\ILocation
     */
    function getLocationByFilter(array $filter);
}