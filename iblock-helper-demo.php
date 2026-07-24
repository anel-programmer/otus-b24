<?php
declare(strict_types=1);

use MyApp\Iblock\Helper;

require_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/header.php';

/**
 * @var CMain $APPLICATION
 */

$APPLICATION->SetTitle('Демо Helper для автозагрузчика');

$id = Helper::getIblockIdByCode('clients_s1');
echo $id;

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';