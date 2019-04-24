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
        <?$APPLICATION->ShowTitle(false);?>
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
            "sebekon:portfolio.form",
            "",
            Array(
                'CLASS' => $arResult['VARIABLES']['CLASS'],
                'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
                'SECTION_CODE' => $arResult['VARIABLES']['SECTION'],
                'ELEMENT_ID' => $arResult['VARIABLES']['ELEMENT_ID'],
                'LIST_URL' => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["list"],
                'AJAX_ADDING_ELEMENT_URL' => $arResult["URL_TEMPLATES"]["ajax_adding_element"],
                'AJAX_DELETING_FILE_URL' => $arResult["URL_TEMPLATES"]["ajax_deleting_file"],

            )
        );?>
    </div>
</div>