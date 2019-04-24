<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);

AddEventHandler("main", "OnBuildGlobalMenu", "notes");
function notes(&$adminMenu, &$moduleMenu){
    $moduleMenu[] = Array(
        "parent_menu" => "global_menu_content",
        "sort"        => 100,
        "url"         => "links_list.php?lang=".LANGUAGE_ID,
        "more_url" => array(
            "links_element_edit.php",
        ),
        "text"        => Loc::getMessage("LINKS_NAME"),
        "icon"        => "blog_menu_icon", // малая иконка
    );
}
?>