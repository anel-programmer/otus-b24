<?

use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("ДЗ #6: Написание своего модуля");

Asset::getInstance()->addCss('//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');


?>
<h1 class="mb-3"><? $APPLICATION->ShowTitle() ?></h1>

<h4 class="mb-3">Пояснительная записка</h4>
<div>
    Разработала модуль Модуль ДЗ6 (myotus.custommodule) в него добавила 2 компонента:
    первый компонент добавила из ДЗ5 "Вывод курса валюты" выводится в CRM на кастомной вкладке "Курс валюты",
    так же разработала компонент который выводит данные из таблицы клиенты с помощью bitrix:main.ui.grid.
    Для отрисовки Tab  использовала событие onEntityDetailsTabsInitialized.
    Модуль myotus.custommodule содержит класс lib\onEntityDetailsTabsInitializedHandler.php  - подключается событие и добавляются вкладки в CRM,
    local/modules/myotus.custommodule/lib/orm/myclientstable.php - ORM класс для работы с кастомной таблицей клеинтов.

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
                <a href="/myotus/"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылка на тестовую страницу с компонентом. Модуль должен быть установлен. 
                </span>
                    <span class="badge bg-warning">
                    Ссылка на просмотр
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_admin.php?lang=ru&path=%2Flocal%2Fmodules%2Fmyotus.custommodule&site=s1"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылка на установленные решения, модуль: ДЗ6 (myotus.custommodule)
                </span>
                    <span class="badge bg-warning">
                    Ссылка на просмотр в админке
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/perfmon_table.php?lang=ru&table_name=myotus_client_lists"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылка на таблицу myotus_client_lists
                </span>
                    <span class="badge bg-warning">
                    Ссылка на просмотр в админке
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=%2Flocal%2Fmodules%2Fmyotus.custommodule%2Flib%2Fonentitydetailstabsinitializedhandler.php&site=s1&lang=ru"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылки на просмотр кода основных файлов ДЗ (onentitydetailstabsinitializedhandler.php) <br>
                    подключается событие и добавляются вкладки в CRM
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=%2Flocal%2Fmodules%2Fmyotus.custommodule%2Flib%2Form%2Fmyclientstable.php&site=s1&lang=ru"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылки на просмотр кода основных файлов ДЗ (myclientstable.php - ORM класс для работы с кастомной таблицей клеинтов)
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>
        </ul>
    </div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>