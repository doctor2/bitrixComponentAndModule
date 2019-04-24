<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<div class="es-portfolio-wrap">
    <h1 class="es-portfolio__title">
        <?$APPLICATION->ShowTitle(false, false);?>
    </h1>

    <div class="es-portfolio__tab">

        <?$APPLICATION->IncludeComponent(
            "sebekon:portfolio.selector",
            "",
            Array(
                'SEF_URL' => $arResult['URL_TEMPLATES']['selector']
            )
        );?>

        <?$APPLICATION->IncludeComponent(
            "sebekon:portfolio.list",
            "",
            Array(
                'CLASS' => $arResult['VARIABLES']['CLASS'],
                'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
                'SECTION_CODE' => $arResult['VARIABLES']['SECTION'],
                'CERTIFICATE_URL' => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["file"],
                'FORM_URL' => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["form"],
                'ELEMENT_FORM_URL' => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element_form"],
                'ELEMENT_ID_ALIAS' => $arResult['ALIASES']['ELEMENT_ID'],
                'AJAX_DELETING_ELEMENT_URL' => $arResult["URL_TEMPLATES"]["ajax_deleting_element"],
                'AJAX_DELETING_FILE_URL' => $arResult["URL_TEMPLATES"]["ajax_deleting_file"],
                'AJAX_ADDING_ELEMENT_URL' => $arResult["URL_TEMPLATES"]["ajax_adding_element"],
            )
        );?>
    </div>
</div>
