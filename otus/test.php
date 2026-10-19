<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Компонент вывода валюты");
?><?$APPLICATION->IncludeComponent(
	"otus:reservation.field",
	"",
	Array()
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>