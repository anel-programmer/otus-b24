<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Компонент вывода валюты");
?><?$APPLICATION->IncludeComponent(
	"otus:currencies.show",
	"",
	Array(
		"CURENT_CURRENCY" => "BYN"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>