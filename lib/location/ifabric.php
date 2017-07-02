<?php
/**
 * Created by PhpStorm.
 * User: fulmine
 * Date: 22.06.2017
 * Time: 0:03
 */

namespace Fulmine\Geo\Location;


interface IFabric
{
    public function createLocation(array $rawData);
}