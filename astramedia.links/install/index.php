<?php
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Application;

Loc::loadMessages(__FILE__);
Class astramedia_links extends CModule
{
    function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__."/version.php");

        $this->MODULE_ID = 'astramedia.links';
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("ASTRAMEDIA_LINKS_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("ASTRAMEDIA_LINKS_MODULE_DESC");

        $this->PARTNER_NAME = Loc::getMessage("ASTRAMEDIA_LINKS_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("ASTRAMEDIA_LINKS_PARTNER_URI");
    }
    function DoInstall()
    {
		\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
		$this->InstallDB();
		$this->InstallFiles();
    }
    function DoUninstall()
    {
        $this->UnInstallDB();
        $this->UnInstallFiles();
        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
    }
    function InstallFiles($arParams = array())
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/astramedia.links/install/bitrix/admin",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFilesEx("/bitrix/admin/links_list.php");
        DeleteDirFilesEx("/bitrix/admin/links_element_edit.php");
        DeleteDirFilesEx("/bitrix/admin/links_get_services.php");
        DeleteDirFilesEx("/bitrix/admin/links_get_subspecies.php");
        DeleteDirFilesEx("/bitrix/admin/links_get_duration.php");
        return true;
    }

    function InstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        if(!Application::getConnection(\Astramedia\Links\LinksTable::getConnectionName())->isTableExists(
            Base::getInstance('\Astramedia\Links\LinksTable')->getDBTableName()
        ))
        {
            Base::getInstance('\Astramedia\Links\LinksTable')->createDbTable();
        }
    }
    function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        Application::getConnection(\Astramedia\Links\LinksTable::getConnectionName())->
        queryExecute('drop table if exists '.Base::getInstance('\Astramedia\Links\LinksTable')->getDBTableName());
    }
}
