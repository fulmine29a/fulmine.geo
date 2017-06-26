<?php
/**
 * Created by PhpStorm.
 * User: fulmine
 * Date: 26.06.2017
 * Time: 20:04
 */

namespace Fulmine\Geo;

class Parser
{
    /** @var  Location\ILocation $location */
    protected $location;
    /** @var  array $replacedFields */
    protected $replacedFields;

    protected $vals;

    function __construct(Location\ILocation $location, array $replacedFields)
    {
        $this->location = $location;
        $this->replacedFields = $replacedFields;
    }
    public function replaceText($string){
        if(empty($vals))
            $this->vals = $this->location->getFields($this->replacedFields);

        return str_replace(
            $this->prepareKeys(),
            $this->prepareValues(),
            $string
        );
    }

    public function registerAsEndBuffer(){
        \Bitrix\Main\EventManager::getInstance()->addEventHandler(
            "main",
            "OnEndBufferContent",
            array(
                $this,
                'onEndBuffer'
            )
        );
    }


    protected function prepareKeys(){
        $valsKeys = array_keys($this->vals);

        foreach ($valsKeys as &$val)
            $val = "#$val#";

        return $valsKeys;
    }

    protected function prepareValues(){
        return array_values($this->vals);
    }

    protected function onEndBuffer(&$content){
        $content = $this->replaceText($content);
    }
}