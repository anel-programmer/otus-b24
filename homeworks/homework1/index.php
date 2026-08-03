<?
use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("ДЗ #1: Создание и настройка проекта в VScode");

Asset::getInstance()->addCss('//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');


?>
<h1 class="mb-3"><? $APPLICATION->ShowTitle() ?></h1>

<h4 class="mb-3">Пояснительная записка</h4>
    <div>
        Портал Б24 развернула на выделенном сервере fvds.ru по адресу: anel.pugacheva.fvds.ru. <br>
        Логин - admin, <br>
        пароль - x1j2dFy3. 
    </div>
<br>
<br>
<hr>




<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>