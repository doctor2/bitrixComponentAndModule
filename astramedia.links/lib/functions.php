<?php
namespace Astramedia\Links;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;
use \Bitrix\Main\Loader;

class Functions
{
    public static function getHlElement($id, $elementId )
    {
        Loader::includeModule('highloadblock');

        $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($id)->fetch();

        if (empty($hlblock)) return;

        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $arFilter = array();
        if($elementId)
            $arFilter = array('UF_XML_ID' => $elementId);
        $arSelect = array("ID", 'UF_NAME', 'UF_XML_ID');
        $rsData = $entity_data_class::getList(array(
            "select" => $arSelect,
            "filter" => $arFilter,
        ));
        $result = array();
        while ($arItem = $rsData->Fetch()) {
            $result[$arItem['UF_XML_ID']] = $arItem['UF_NAME'];
        }
        return $result;
    }

}