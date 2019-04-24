<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader,
    Bitrix\Main\Application,
    Astramedia\Links,
    Bitrix\Main\Type;
use \Bitrix\Main\Page\Asset;
Asset::getInstance()->addJs("/local/templates/ozarign/js/jquery-2.2.0.min.js");

IncludeModuleLangFile(__FILE__);

$request = Application::getInstance()->getContext()->getRequest();
$lang = Application::getInstance()->getContext()->getLanguage();
$siteId = Application::getInstance()->getContext()->getSite();
$linksId = intval($request->get('ID'));

Loader::includeModule('astramedia.links');
Loader::includeModule('iblock');

if ($REQUEST_METHOD=="POST" && check_bitrix_sessid() && CUser::IsAdmin())
{
    if($request->getPost('ID') != '' && $request->getPost('update') == 'Y'){
        $arOrder = array('ID' => 'ASC');
        $arFilter = array('=ID'=>$request->getPost('ID'));
        $arSelect = array('*');

        $arLinks = Links\LinksTable::getList(array(
            'select' => $arSelect,
            'filter' => $arFilter,
            'order' => $arOrder,
        ))->fetch();
        if(!empty($arLinks)) {
            Links\LinksTable::update($request->getPost('ID'),array(
                "RHYTHMOLOGIST_ID" => $request->getPost('RHYTHMOLOGIST_ID'),
                "SERVICE_ID" => $request->getPost('SERVICE_ID'),
                "SUBSPECIES_SERVICES_ID" => $request->getPost('SUBSPECIES_SERVICES_ID'),
                "DURATION_OR_TYPE_ID" => $request->getPost('DURATION_OR_TYPE_ID'),
                "LINK" => $request->getPost('LINK'),
                "DATE_UPDATE" => new Type\DateTime,
            ));
            if (strlen($apply) <= 0 && empty($errors))
                LocalRedirect("/bitrix/admin/links_list.php?lang=" . LANG . GetFilterParams("filter_", false));
        }
        else{
            $errors = array(
                "MESSAGE"=>Loc::getMessage('LINKS_ERROR'),
                "DETAILS"=> Loc::getMessage('LINKS_ERROR'),
                "HTML"=>true,
                "TYPE"=>"ERROR",
            );
        }
    }
    else{
        Links\LinksTable::add(array(
            "RHYTHMOLOGIST_ID" => $request->getPost('RHYTHMOLOGIST_ID'),
            "SERVICE_ID" => $request->getPost('SERVICE_ID'),
            "SUBSPECIES_SERVICES_ID" => $request->getPost('SUBSPECIES_SERVICES_ID'),
            "DURATION_OR_TYPE_ID" => $request->getPost('DURATION_OR_TYPE_ID'),
            "LINK" => $request->getPost('LINK'),
        ));
        if (strlen($apply) <= 0 && empty($errors))
            LocalRedirect("/bitrix/admin/links_list.php?lang=" . LANG . GetFilterParams("filter_", false));
    }

}
if($linksId != '') {
    $arOrder = array('ID' => 'ASC');
    $arFilter = array('=ID' => $linksId);
    $arSelect = array('*');
    $arLinks = Links\LinksTable::getList(array(
        'select' => $arSelect,
        'filter' => $arFilter,
        'order' => $arOrder,
    ))->fetch();

    $arSort = array();
    $arSelect = Array("ID", 'PROPERTY_SERVICES', 'PROPERTY_SERVICES.NAME');
    $arFilter = Array("IBLOCK_ID" => RHYTHMOLOGIST_IBLOCK_ID, "=ID" => $arLinks['RHYTHMOLOGIST_ID']);
    $res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
    $services = array();
    while ($ob = $res->GetNextElement()) {
        $arItem = $ob->GetFields();
        $services[$arItem['PROPERTY_SERVICES_VALUE']] = $arItem['PROPERTY_SERVICES_NAME'];
    }

    $arSort = array();
    $arSelect = Array("ID", 'PROPERTY_SUBSPECIES_SERVICES', 'PROPERTY_SUBSPECIES_SERVICES.NAME');
    $arFilter = Array("IBLOCK_ID" => SERVICES_IBLOCK_ID, "=ID" => $arLinks['SERVICE_ID']);
    $res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
    $subspecies = array();
    while ($ob = $res->GetNextElement()) {
        $arItem = $ob->GetFields();
        $subspecies[$arItem['PROPERTY_SUBSPECIES_SERVICES_VALUE']] = $arItem['PROPERTY_SUBSPECIES_SERVICES_NAME'];
    }


    $arSort = array();
    $arSelect = Array("ID",
        'PROPERTY_DURATION_OF_THE_SERVICE',
        'PROPERTY_TYPE_OF_THE_SERVICE'
    );
    $arFilter = Array("IBLOCK_ID" => SUBSPECIES_SERVICES_IBLOCK_ID, "=ID" => $arLinks['SUBSPECIES_SERVICES_ID']);
    $res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
    $durationId = array();
    $durationOrType = array();
    while ($ob = $res->GetNextElement())
    {
        $arItem = $ob->GetFields();
        if ($arItem['PROPERTY_TYPE_OF_THE_SERVICE_VALUE'])
            $durationOrType[$arItem['PROPERTY_TYPE_OF_THE_SERVICE_VALUE_ID']] = $arItem['PROPERTY_TYPE_OF_THE_SERVICE_VALUE'];
        elseif ($arItem['PROPERTY_DURATION_OF_THE_SERVICE_VALUE'])
            $durationId[] = $arItem['PROPERTY_DURATION_OF_THE_SERVICE_VALUE'];
    }
    if ($durationId )
    {
        $durationOrType = \Astramedia\Links\Functions::getHlElement(HL_DURATION_OF_THE_SERVICE_ID, $durationId);
    }

}



$rsRhyth = \Bitrix\Iblock\ElementTable::getList(array(
    'select' => array("ID", 'NAME'),
    'filter' => array('=IBLOCK_ID' => RHYTHMOLOGIST_IBLOCK_ID),
));
while($arItem = $rsRhyth->fetch()){
    $rhyths[] = $arItem;
}



require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$aMenu = array(
    array(
        "TEXT" => Loc::getMessage("LINKS_LIST"),
        "LINK" => "/bitrix/admin/links_list.php?lang=".LANG.GetFilterParams("filter_"),
        "ICON"	=> "btn_list",
        "TITLE" => Loc::getMessage("LINKS_LIST_TITLE"),
    )
);
$context = new CAdminContextMenu($aMenu);
$context->Show();

if(!empty($errors))
    CAdminMessage::ShowMessage($errors);
if($accesspageId != '')
    $APPLICATION->SetTitle('Изменение записи');
else
    $APPLICATION->SetTitle('Добавить записи');
?>
    <form method="POST" action="<?=$APPLICATION->GetCurPage()."?lang=".$lang.'&ID='.$linksId?>" name="form1" id="form1">
        <?if($linksId != '' && $request->get('action') != 'copy'){?>
            <input type="hidden" name="update" value="Y">
            <input type="hidden" name="ID" id="ID" value="<?=$linksId;?>">
        <?}else{?>
            <input type="hidden" name="add" value="Y">
        <?}?>
        <input type="hidden" name="lang" id="lang" value="<?=$lang;?>">
        <?=bitrix_sessid_post()?>
        <?
        $aTabs = array(
            array("DIV" => "edit1", "TAB" => Loc::getMessage("LINKS_EDIT"), "ICON" => "sale", "TITLE" => Loc::getMessage("LINKS_EDIT"))
        );

        $tabControl = new CAdminTabControl("tabControl", $aTabs);
        $tabControl->Begin();
        ?>

        <?
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td width="40%"><?=Loc::getMessage('LINKS_RHYTHMOLOGIST_ID')?></td>
            <td width="60%">
                <select name="RHYTHMOLOGIST_ID" id="rhythmologist">
                    <option value="">Выберите ритмолога</option>
                    <?foreach ($rhyths as $arItem):?>
                        <option value="<?=$arItem['ID']?>" <?= ($linksId && $arLinks['RHYTHMOLOGIST_ID'] === $arItem['ID'])?'selected':'';?>><?=$arItem['NAME']?></option>
                    <?endforeach;?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=Loc::getMessage('LINKS_SERVICE_ID')?></td>
            <td width="60%">
                <select name="SERVICE_ID" id="service">
                    <option value="">Выберите услугу</option>
                    <?foreach ($services as $key => $value):?>
                        <??>
                        <option value="<?=$key?>" <?= ($linksId && $arLinks['SERVICE_ID'] == $key)?'selected':'';?>><?=$value?></option>
                    <?endforeach;?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=Loc::getMessage('LINKS_SUBSPECIES_SERVICES_ID')?></td>
            <td width="60%">
                <select name="SUBSPECIES_SERVICES_ID" id="subspecies">
                    <option value="">Выберите подвид услуги</option>
                    <?foreach ($subspecies as $key => $value):?>
                        <option value="<?=$key?>" <?= ($linksId && $arLinks['SUBSPECIES_SERVICES_ID'] == $key)?'selected':'';?>><?=$value?></option>
                    <?endforeach;?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=Loc::getMessage('LINKS_DURATION_OR_TYPE_ID')?></td>
            <td width="60%">
                <select name="DURATION_OR_TYPE_ID" id="duration">
                    <option value="">Выберите время(тип) услуги</option>
                    <?foreach ($durationOrType as $key => $value):?>
                        <option value="<?=$key?>" <?= ($linksId && $arLinks['DURATION_OR_TYPE_ID'] == $key)?'selected':'';?>><?=$value?></option>
                    <?endforeach;?>
                </select>
            </td>
        </tr>
        <tr>
            <td><?=Loc::getMessage('LINKS_LINK')?></td>
            <td>
                <textarea  cols="50" name="LINK"><?=$arLinks['LINK']?></textarea>
            </td>
        </tr>
        <?
        $tabControl->EndTab();
        ?>

        <?
        $tabControl->Buttons(
            array(
                "back_url" => "/bitrix/admin/links_list.php?lang=".LANG.GetFilterParams("filter_")
            )
        );
        ?>

        <?
        $tabControl->End();
        ?>
    </form>
    <script>
        (function ($, window) {

            $(function () {
                $('#rhythmologist').on('change', function (e) {
                    var id = $(this).find('option:selected').val();
                    console.log(id);
                    $.ajax({
                        type: 'GET',
                        dataType: "html",
                        url: '/bitrix/admin/links_get_services.php',
                        data: {'rhythmologist_id' : id },

                    }).done(function (data) {
                        $('#service').children().not('option:first').remove();
                        $('#subspecies').children().not('option:first').remove();
                        $('#duration').children().not('option:first').remove();
                        $('#service option:first').after(data);
                    }).fail(function () {
                        console.log('error');
                    })
                });

                $('#service').on('change', function (e) {
                    var id = $(this).find('option:selected').val();
                    $.ajax({
                        type: 'GET',
                        dataType: "html",
                        url: '/bitrix/admin/links_get_subspecies.php',
                        data: {'service_id' : id },

                    }).done(function (data) {
                        $('#subspecies').children().not('option:first').remove();
                        $('#duration').children().not('option:first').remove();
                        $('#subspecies option:first').after(data);
                    }).fail(function () {
                        console.log('error');
                    })
                });

                $('#subspecies').on('change', function (e) {
                    var id = $(this).find('option:selected').val();
                    $.ajax({
                        type: 'GET',
                        dataType: "html",
                        url: '/bitrix/admin/links_get_duration.php',
                        data: {'subspecies_id' : id },

                    }).done(function (data) {
                        $('#duration').children().not('option:first').remove();
                        $('#duration option:first').after(data);
                    }).fail(function () {
                        console.log('error');
                    })
                });
            })
        })($, window)
    </script>
<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");