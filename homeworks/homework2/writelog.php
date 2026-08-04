<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("Добавление в лог");
?>
    <ul class="list-group">
        <li class="list-group-item">
            <a href="/local/logs/log_custom.log">Файл лога</a>,
            в лог добавленно 'Открыта страница writelog.php'
        </li>
        <li class="list-group-item">
            <a href="clearlog.php">Очистить</a> файл лога
        </li>
         <li class="list-group-item">
            <a href="/homeworks/homework2/">Вернуться</a> на страницу задания
        </li>
    </ul>
<?
//  ДОБАВЛЕНИЯ В ЛОГ
App\Debug\Log::addLog('Открыта страница writelog.php',false,"log_custom",false);
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>