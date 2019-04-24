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
use Bitrix\Highloadblock as HL;


Loc::loadLanguageFile(__FILE__);

function process()
{
    $request = Application::getInstance()->getContext()->getRequest();

    if ($request->get('subspecies_id') && CModule::IncludeModule('iblock'))
    {
        $arSort = array();
        $arSelect = Array("ID",
            'PROPERTY_DURATION_OF_THE_SERVICE',
            'PROPERTY_TYPE_OF_THE_SERVICE'
        );
        $arFilter = Array("IBLOCK_ID" => SUBSPECIES_SERVICES_IBLOCK_ID, "=ID" => $request->get('subspecies_id'));
        $res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
        $durationId = array();
        while ($ob = $res->GetNextElement())
        {
            $arItem = $ob->GetFields();
//            dump($arItem);
            if ($arItem['PROPERTY_TYPE_OF_THE_SERVICE_VALUE'])
                echo "<option value=\"{$arItem['PROPERTY_TYPE_OF_THE_SERVICE_VALUE_ID']}\">{$arItem['PROPERTY_TYPE_OF_THE_SERVICE_VALUE']}</option>";
            elseif ($arItem['PROPERTY_DURATION_OF_THE_SERVICE_VALUE'])
                $durationId[] = $arItem['PROPERTY_DURATION_OF_THE_SERVICE_VALUE'];
        }
        if ($durationId && CModule::IncludeModule('astramedia.links'))
        {
            $durations = \Astramedia\Links\Functions::getHlElement(HL_DURATION_OF_THE_SERVICE_ID, $durationId);

            foreach ($durations as $key =>$value) {
                echo "<option value=\"{$key}\">{$value}</option>";
            }
        }
    }
}

process();
