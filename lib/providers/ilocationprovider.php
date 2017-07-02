<?php
/**
 * Created by PhpStorm.
 * User: fulmine
 * Date: 15.06.2017
 * Time: 20:32
 */

namespace Fulmine\Geo\Providers;


interface ILocationProvider
{
    function getLocationAsFilter();
}