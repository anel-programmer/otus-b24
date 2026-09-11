<?

use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("ДЗ #5: Компонент списка таблицы БД");

Asset::getInstance()->addCss('//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');

?>
    <h1 class="mb-3"><? $APPLICATION->ShowTitle() ?></h1>

    <h4 class="mb-3">Пояснительная записка</h4>
    <div>
        Ссылка на GitHab - <a href="https://github.com/anel-programmer/otus-b24/tree/homework5" >https://github.com/anel-programmer/otus-b24/tree/homework5</a>
        <ol>
            <li>Создала компонент для вывода курса валюты (currencies.show) в каталоге local в пространстве имен otus </li>
            <li>В файле /otus/currencies.php подключила свой компонент</li>
        </ol>
    </div>
    <br>
    <br>
    <hr>

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-success text-white">
            Файлы проекта
        </div>
        <ul class="list-group list-group-flush">

            <li class="list-group-item list-group-item-action">
                <a href="/otus/currencies.php"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылка на тестовую страницу с компонентом
                </span>
                    <span class="badge bg-warning">
                    Ссылка на просмотр
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_admin.php?PAGEN_1=1&SIZEN_1=20&lang=ru&site=s1&path=%2Flocal%2Fcomponents%2Fotus%2Fcurrencies.show&show_perms_for=0&fu_action="
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылки на просмотр кода файлов компонента currencies.show
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=%2Fotus%2Fcurrencies.php&site=s1&lang=ru"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылки на просмотр кода файла с подключением компонента
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>
        </ul>
    </div>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>