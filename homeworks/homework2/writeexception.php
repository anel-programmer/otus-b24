<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Ошибка для exeption");
?>
<ul class="list-group">
    <li class="list-group-item">
        <a href="/local/logs/exceptions.log">Файл лога</a>
    </li>
    <li class="list-group-item">
        <a href="clearexception.php">Очистить</a> файл лога /local/logs/exceptions.log
    </li>
    <li class="list-group-item">
        <a href="/homeworks/homework2/">Вернуться</a> на страницу задания
    </li>
</ul>
<?
// ошибка для exeption
$res = 1/0;
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
