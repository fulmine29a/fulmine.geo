<?php
/**
 * Created by PhpStorm.
 * User: fulmine
 * Date: 24.06.2017
 * Time: 21:24
 */

namespace Fulmine\Geo\Providers;


class SXGeoProvider implements ILocationProvider
{
    private $sxgeo;

    function __construct()
    {
        $path = dirname(dirname(__DIR__));
        require_once $path.'/ext_lib/SXGeo/SxGeo.php';
        $this->sxgeo = new \SxGeo($path.'/ext_lib/SXGeo/SxGeoCity.dat');
    }

    function getLocationAsFilter()
    {
        $enc = mb_internal_encoding();
        mb_internal_encoding("8bit");


        // TODO: uncomment work code
        //$info = $this->sxgeo->getCityFull($_SERVER['REMOTE_ADDR']);
        $info = $this->sxgeo->getCityFull('88.201.128.12'); // SPB

        mb_internal_encoding($enc);
        return array(
            'COUNTRY' => $info['country']['iso'],
            'REGION' => $info['region']['iso'],
            'CITY' => $info['city']['name_en']
        );
    }
}