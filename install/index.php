<?php
/**
 * Created by PhpStorm.
 * User: fulmine
 * Date: 14.06.2017
 * Time: 21:54
 */
Class fulmine_geo extends CModule
{
    var $MODULE_ID = "fulmine.geo";
    var $MODULE_NAME = 'Fulmine Geo module';
    var $PARTNER_NAME = 'fulmine';

    function DoInstall()
    {
        RegisterModule($this->MODULE_ID);
        symlink(
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID.'/install/components',
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/".$this->MODULE_ID
        );
    }

    function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);
        unlink($_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/".$this->MODULE_ID);
    }
}