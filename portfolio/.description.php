<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SEBEKON_PORTFOLIO_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("SEBEKON_PORTFOLIO_COMPONENT_DESCR"),
    "COMPLEX" => "Y",
    "PATH" => array(
		"ID" => "sebekon",
        "NAME" => GetMessage("SEBEKON_PORTFOLIO_SECTION_NAME"),
        "SORT" => 10000,
        "CHILD" => array(
            "ID" => "portfolio",
            "NAME" => GetMessage("SEBEKON_PORTFOLIO_PARENT"),
            "SORT" => 10,
        ),

    ),
);
?>