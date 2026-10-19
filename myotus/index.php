<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Компонент списка таблицы базы данных из модуля myotus.custommodule");


$APPLICATION->IncludeComponent(
    "myotus:client.table", 
    "list", 
    array(
        "COMPONENT_TEMPLATE" => "list",
        "SHOW_CHECKBOXES" => "Y",
        "NUM_PAGE" => "3"
    ),
    false
);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>