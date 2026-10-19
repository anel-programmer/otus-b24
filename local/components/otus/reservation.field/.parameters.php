<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
use Bitrix\Main\Loader;

if(!CModule::IncludeModule("iblock"))
	return;

$arComponentParameters = array(
 /*	"GROUPS" => array(
		"LIST"=>array(
			"NAME"=>GetMessage("CURRENCIES_PARAMETERS"),
			"SORT"=>"300"
		)
	),
	"PARAMETERS" => array(
		"CURRENT_VALUES" =>  array(
			"PARENT" => "LIST",
			"NAME"=>GetMessage("CURRENCIES_LIST"),
			"TYPE"=>"LIST",
			"VALUES" => $currencyList,
            "REFRESH" => "Y"
		)
	)*/
);


