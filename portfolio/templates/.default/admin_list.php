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
                'STUDENT_ID' => $arResult['VARIABLES']['STUDENT_ID'],
                'DETAIL_URL' => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["admin_detail"],
                'ELEMENT_ID_ALIAS' => $arResult['ALIASES']['ELEMENT_ID'],
                'IS_ADMIN' => $arResult['IS_ADMIN'],
            )
        );?>
    </div>
</div>
