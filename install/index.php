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
    var $MODULE_NAME;

    function DoInstall()
    {
        /*global $DB, $APPLICATION, $step;
        $APPLICATION->IncludeAdminFile(GetMessage("FORM_INSTALL_TITLE"),
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/mymodule/install/step1.php");*/
        RegisterModule($this->MODULE_ID);
        symlink(
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID.'/install/components',
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/".$this->MODULE_ID
        );
    }

    function DoUninstall()
    {
        /*global $DB, $APPLICATION, $step;
        $APPLICATION->IncludeAdminFile(GetMessage("FORM_INSTALL_TITLE"),
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/mymodule/install/unstep1.php");
        */
        UnRegisterModule($this->MODULE_ID);
    }
}