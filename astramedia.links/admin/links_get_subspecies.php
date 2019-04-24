<?
define("PULL_AJAX_INIT", true);
define("PUBLIC_AJAX_MODE", true);
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC", "Y");
define("NO_AGENT_CHECK", true);
define("NOT_CHECK_PERMISSIONS", true);
define("DisableEventsCheck", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;

Loc::loadLanguageFile(__FILE__);

function process()
{
    $request = Application::getInstance()->getContext()->getRequest();

    if ($request->get('service_id') && CModule::IncludeModule('iblock')) {
        $arSort = array();
        $arSelect = Array("ID", 'PROPERTY_SUBSPECIES_SERVICES', 'PROPERTY_SUBSPECIES_SERVICES.NAME');
        $arFilter = Array("IBLOCK_ID" => SERVICES_IBLOCK_ID, "=ID" => $request->get('service_id'));
        $res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arItem = $ob->GetFields();
            echo "<option value=\"{$arItem['PROPERTY_SUBSPECIES_SERVICES_VALUE']}\">{$arItem['PROPERTY_SUBSPECIES_SERVICES_NAME']}</option>";
        }
    }
}

process();
