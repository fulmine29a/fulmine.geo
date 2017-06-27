<?php
/**
 * Created by PhpStorm.
 * User: fulmine
 * Date: 27.06.2017
 * Time: 23:13
 */

namespace Fulmine\Geo\Location;


class GlobalLocation extends BasicLocation
{
    function __construct(array $rawData)
    {
        parent::__construct($rawData);
        $this->fillBaseUrl();
    }
    function isGlobal()
    {
        return true;
    }
    function __wakeup()
    {
        $this->fillBaseUrl();
    }

    protected function fillBaseUrl(){
        $site = \Bitrix\Main\SiteTable::GetByID(SITE_ID)->fetch();
        $this->rawData['BASE_URL'] = array($site['SERVER_NAME']);
    }
}