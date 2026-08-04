<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
// ОЧИСТКИ ЛОГА
App\Debug\Log::cleanLog("log_custom");

LocalRedirect('/homeworks/homework2/');
