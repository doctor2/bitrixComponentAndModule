<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "SEF_FOLDER" => Array(
            "NAME" => Loc::GetMessage("SE_PORTFOLIO_SEF_FOLDER"),
            "TYPE" => "STRING",
            "DEFAULT" => '',
            "PARENT" => "BASE",
        ),
        "CACHE_TIME"  =>  Array("DEFAULT"=>1800000),

    )
);


?>