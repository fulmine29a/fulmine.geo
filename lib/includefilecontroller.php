<?php
/**
 * Created by PhpStorm.
 * User: fulmine
 * Date: 02.07.2017
 * Time: 22:17
 */

namespace Fulmine\Geo;


class IncludeFileController
{
    const INCLUDES_ROOT = '/location_includes';

    static function getFileNameForThisLocation($pathFromRoot, \Fulmine\Geo\Location\ILocation $location){
        return \Bitrix\Main\Application::getDocumentRoot().static::getFileNameWithSuffixes($pathFromRoot, $location->getFileSuffixes());
    }

    static function findFileForLocation($pathFromRoot, \Fulmine\Geo\Location\ILocation $location){
        $root = \Bitrix\Main\Application::getDocumentRoot();

        $suffixes = $location->getFileSuffixes();

        while(
            !($succ = file_exists(  $file = $root.static::getFileNameWithSuffixes($pathFromRoot, $suffixes)  ))
            and
            (count($suffixes))
        ){
            array_pop($suffixes);
        }

        if($succ)
            return $file;
        else
            if(file_exists($root.static::INCLUDES_ROOT.$pathFromRoot))
                return $root.static::INCLUDES_ROOT.$pathFromRoot;
            else
                return false;
    }

    protected static function getFileNameWithSuffixes($pathFromRoot, array $suffixes){
        $fileInfo = pathinfo($pathFromRoot);

        foreach ($suffixes as $suffix)
            if($suffix)
               $fileInfo['filename'].='.'.$suffix;

        return static::INCLUDES_ROOT .
            (($fileInfo['dirname'] != '/') ? $fileInfo['dirname'] : '' ). DIRECTORY_SEPARATOR
            . $fileInfo['filename']
            .'.'. $fileInfo['extension'];
    }
}