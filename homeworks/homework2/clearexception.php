<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
// ОЧИСТКИ ЛОГА
App\Debug\Log::cleanLog("exceptions");

LocalRedirect('/homeworks/homework2/');
