<?php
/**
 * Created by PhpStorm.
 * User: fulmine
 * Date: 24.06.2017
 * Time: 20:27
 */

namespace Fulmine\Geo\Location;


class StdFabric implements IFabric
{

    public function createLocation(array $rawData)
    {
        return ($rawData['GLOBAL'] === 'Y') ? new GlobalLocation($rawData) : new BasicLocation($rawData);
    }
}