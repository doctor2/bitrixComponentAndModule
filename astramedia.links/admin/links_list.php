<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader,
    Astramedia\Links,
    Bitrix\Main\Application,
    Bitrix\Main\UserTable;
if ($USER->IsAdmin()) {
    $APPLICATION->ShowHeadStrings();
}
IncludeModuleLangFile(__FILE__);

Loader::includeModule('astramedia.links');
Loader::includeModule('iblock');


$APPLICATION->SetTitle(Loc::getMessage("LINKS_TITLE"));

$sTableID = "links_table";
$curPage = Application::getInstance()->getContext()->getCurrent()->getRequest()->getRequestUri();
$lang = Application::getInstance()->getContext()->getLanguage();
$request = Application::getInstance()->getContext()->getRequest();
$nPageSize = CAdminResult::GetNavSize($sTableID);

$adminSort  = new CAdminSorting($sTableID, "ID", "DESC");
$adminList = new CAdminList($sTableID, $adminSort);


if($request->getQuery('action_button') == 'delete'){
    if($request->getQuery('ID') != ''){
        $arOrder = array('ID' => 'ASC');
        $arFilter = array('=ID'=>IntVal($request->getQuery('ID')));
        $arSelect = array('*');

        $arLink = Links\LinksTable::getList(array(
            'select' => $arSelect,
            'filter' => $arFilter,
            'order' => $arOrder,
        ))->fetch();
        if(!empty($arLink)){
            @set_time_limit(0);
            $arLink = Links\LinksTable::delete($request->getQuery('ID'));
            $adminList->AddActionSuccessMessage(Loc::getMessage("LINKS_SUCCESS_DELETE"));
        }
        else{

        }
    }
    else{

    }
}

$arFilter = array();

// опишем элементы фильтра
$FilterArr = Array(
    "find_id",
    "find_rhyth_name",
);

// инициализируем фильтр
$adminList->InitFilter($FilterArr);

if (IntVal($find_id)>0)
    $arFilter["ID"] = IntVal($find_id);
if ($find_rhyth_name)
    $arFilter["RHYTHMOLOGIST.NAME"] = '%'.$find_rhyth_name.'%';
if ($request->getQuery('by')){
    $arOrder = array($request->getQuery('by') => $request->getQuery('order'));
}else{
    $arOrder = array('ID' => 'ASC');
}

$arSelect = array('*', 'RHYTHMOLOGIST_NAME' => 'RHYTHMOLOGIST.NAME',
    'SERVICE_NAME' => 'SERVICE.NAME',
    'SUBSPECIES_SERVICES_NAME' => 'SUBSPECIES_SERVICES.NAME',
);

$nav = new \Bitrix\Main\UI\AdminPageNavigation("nav-links");

$rsLinks = Links\LinksTable::getList(array(
    'select' => $arSelect,
    'filter' => $arFilter,
    'order' => $arOrder,
    "count_total" => true,
    "offset" => $nav->getOffset(),
    "limit" => $nav->getLimit()
));

$arLinks = array();
$durationId = array();
$typeId = array();
while($arLink = $rsLinks->fetch()){
    $arLinks[] = $arLink;
    if (preg_match('/[a-zA-Z]+/', $arLink['DURATION_OR_TYPE_ID']))
        $durationId[] = $arLink['DURATION_OR_TYPE_ID'];
    elseif (intval($arLink['DURATION_OR_TYPE_ID']) > 0 )
        $typeId[] = $arLink['DURATION_OR_TYPE_ID'];
}
$durationOrType = array();
if ($durationId )
{
    $durationOrType = \Astramedia\Links\Functions::getHlElement(HL_DURATION_OF_THE_SERVICE_ID, $durationId);
}

if ($typeId)
{
    $arSort = array();
    $arSelect = Array("ID", 'PROPERTY_TYPE_OF_THE_SERVICE');
    $arFilter = Array("ID" => $subspeciesId, 'IBLOCK_ID' => SUBSPECIES_SERVICES_IBLOCK_ID, "ACTIVE" => "Y");
    $res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arItem = $ob->GetFields();
        if (in_array($arItem['PROPERTY_TYPE_OF_THE_SERVICE_VALUE_ID'], $typeId))
            $durationOrType[$arItem['PROPERTY_TYPE_OF_THE_SERVICE_VALUE_ID']] =  $arItem['PROPERTY_TYPE_OF_THE_SERVICE_VALUE'];
    }
}




$adminList->AddHeaders(array(
    array("id"=>"ID", "content"=>"ID", "sort"=>"ID", "default"=>true),
    array("id"=>"RHYTHMOLOGIST_NAME", "content"=>Loc::getMessage("LINKS_RHYTHMOLOGIST"), "sort"=>"RHYTHMOLOGIST_NAME", "default"=>true),
    array("id"=>"SERVICE_NAME","content"=>Loc::getMessage("LINKS_SERVICE"), "sort"=>"SERVICE_NAME", "default"=>true),
    array("id"=>"SUBSPECIES_SERVICES_NAME","content"=>Loc::getMessage("LINKS_SUBSPECIES_SERVICES"), "sort"=>"SUBSPECIES_SERVICES_NAME", "default"=>true),
    array("id"=>"DURATION_OR_TYPE_ID","content"=>Loc::getMessage("LINKS_DURATION_OR_TYPE"), "default"=>true),
    array("id"=>"LINK", "content"=>Loc::getMessage('LINKS_LINK'),"sort"=>"LINK", "default"=>true),
    array("id"=>"DATE_CREATE", "content"=>Loc::getMessage('LINKS_DATE_CREATE'),"sort"=>"DATE_CREATE", "default"=>true),
    array("id"=>"DATE_UPDATE", "content"=>Loc::getMessage('LINKS_DATE_UPDATE'),"sort"=>"DATE_UPDATE", "default"=>false),
));


foreach($arLinks as $arLink ){
    $row =& $adminList->AddRow($arLink['ID'], $arLink);
    
    $row->AddField("ID", $arLink['ID']);
    $row->AddField("RHYTHMOLOGIST_NAME", $arLink['RHYTHMOLOGIST_NAME']);
    $row->AddField("SERVICE_NAME", $arLink['SERVICE_NAME']);
    $row->AddField("SUBSPECIES_SERVICES_NAME", $arLink['SUBSPECIES_SERVICES_NAME']);
    $row->AddField("DURATION_OR_TYPE_ID", $durationOrType[$arLink['DURATION_OR_TYPE_ID']]);
    $row->AddField("LINK", $arLink['LINK']);
    $row->AddField("DATE_CREATE", $arLink['DATE_CREATE']);
    $row->AddField("DATE_UPDATE", $arLink['DATE_UPDATE']);

    $arActions = array();
    $arActions[] = array("ICON"=>"edit", "TEXT"=>Loc::getMessage("EDIT_LINKS_ALT"), "ACTION"=>$adminList->ActionRedirect("links_element_edit.php?ID=".$arLink['ID']."&lang=".$lang.GetFilterParams("filter_").''), "DEFAULT"=>true);
    $arActions[] = array("ICON"=>"copy", "TEXT"=>Loc::getMessage("COPY_LINKS_ALT"), "ACTION"=>$adminList->ActionRedirect("links_element_edit.php?ID=".$arLink['ID']."&lang=".$lang.GetFilterParams("filter_")."&action=copy"), "DEFAULT"=>true);
    $arActions[] = array("SEPARATOR" => true);
    $arActions[] = array("ICON"=>"delete", "TEXT"=>Loc::getMessage("DELETE_LINKS_ALT"), "ACTION"=>"if(confirm('".Loc::getMessage('DELETE_LINKS_CONFIRM')."')) ".$adminList->ActionDoGroup($arLink['ID'], "delete"));

    $row->AddActions($arActions);
}
$nav->setRecordCount($rsLinks->getCount());
$adminList->setNavigation($nav, "Навигация");
$adminList->CheckListMode();

/*Добовляем кнопки*/
$aContext = Array();
$aContext = array(
        array(
            "TEXT" => Loc::getMessage("LINKS_ADD_NEW"),
            "LINK" => "links_element_edit.php?lang=".LANG.GetFilterParams("filter_"),
            "TITLE" => Loc::getMessage("LINKS_ADD_NEW_ALT"),
            "ICON" => "btn_new"
        ),
    );

$adminList->AddAdminContextMenu($aContext);
/**/

$adminList->CheckListMode();

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
    // создадим объект фильтра
    $oFilter = new CAdminFilter(
        $sTableID."_filter",
        array(
            "ID",
            GetMessage("LINKS_RHYTHMOLOGIST"),
        )
    );
    ?>
    <form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
        <?$oFilter->Begin();?>
        <tr>
            <td><?="ID"?>:</td>
            <td>
                <input type="text" name="find_id" size="47" value="<?echo htmlspecialchars($find_id)?>">
            </td>
        </tr>
        <tr>
            <td><?=GetMessage("LINKS_RHYTHMOLOGIST").":"?></td>
            <td><input type="text" name="find_rhyth_name" size="47" value="<?echo htmlspecialchars($find_rhyth_name)?>"></td>
        </tr>
        <?
        $oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
        $oFilter->End();
        ?>
    </form>
<?
$adminList->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");