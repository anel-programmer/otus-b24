<?
define("STOP_STATISTICS", true);
define("NO_KEEP_STATISTIC", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Context;
use Bitrix\Main\Loader;

$request = Context::getCurrent()->getRequest();
$response = [];

// Проверяем права пользователя на доступ в админку, сессию и модуль
if ($GLOBALS["USER"]->IsAdmin() && $request->isPost() && check_bitrix_sessid() && Loader::includeModule("iblock")) {
    
    $typeId = trim($request->getPost("iblock_type_id"));

    if (!empty($typeId)) {
        $dbIblocks = CIBlock::GetList(
            array("SORT" => "ASC", "NAME" => "ASC"),
            array("TYPE" => $typeId, "ACTIVE" => "Y") // В админке обычно выводим инфоблоки всех сайтов
        );
        
        while ($arIblock = $dbIblocks->Fetch()) {
            $response[] = array(
                "ID" => $arIblock["ID"],
                "NAME" => $arIblock["NAME"]
            );
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>