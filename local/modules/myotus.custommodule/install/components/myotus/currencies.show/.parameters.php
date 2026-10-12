<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
use Bitrix\Main\Loader;
use Bitrix\Currency\CurrencyTable;

if(!CModule::IncludeModule("iblock"))
	return;
Loader::includeModule('currency');

// Получаем список всех валют
$result = CurrencyTable::getList([
    'select' => ['CURRENCY'],
    'order'  => ['SORT' => 'ASC']
]);

$currencyList = array();
while ($currency = $result->fetch()) {
	$currencyList[$currency['CURRENCY']] = $currency['CURRENCY'];
}

$arComponentParameters = array(
	"GROUPS" => array(
		"LIST"=>array(
			"NAME"=>GetMessage("CURRENCIES_PARAMETERS"),
			"SORT"=>"300"
		)
	),
	"PARAMETERS" => array(
		"CURENT_CURRENCY" =>  array(
			"PARENT" => "LIST",
			"NAME"=>GetMessage("CURRENCIES_LIST"),
			"TYPE"=>"LIST",
			"VALUES" => $currencyList,
            "REFRESH" => "Y"
		)
	)
);


