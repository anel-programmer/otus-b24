<?php
require_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/header.php';
/**
 * @var CMain $APPLICATION
 */

$APPLICATION->SetTitle('Демо библиотеки Var-Dumper');
dump([
    '1'=>'11',
    '2'=>[
        '21'=>[
            '213'=>'hello'
        ],
    ],
]);

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';