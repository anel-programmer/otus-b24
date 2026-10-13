<?

use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("ДЗ #9: Написание своих активити для БП");

Asset::getInstance()->addCss('//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');


?>
<h1 class="mb-3"><? $APPLICATION->ShowTitle() ?></h1>

<h4 class="mb-3">Пояснительная записка</h4>
<div>
     Ссылка на GitHab - https://github.com/anel-programmer/otus-b24/tree/homework9 
       <ol>
        <li>Создала список "Заказчики" и установила галочку "Включить поддержку бизнес-процессов" в настройках списка</li>
        <li>Создала кастомный активити local/activities/custom/companybyinnactivity, под названием "Компания по инн" в разделе "Прочее"
            <p>Этот активити выполняет получение данных компании по инн и добавление компании в CRM -> Компании</p>
        </li>
        <li>Для списка "Заказчики" создала последовательный бизнес-процесс "Данные компании из сервиса DADATA", который запускается при добавлении элемента.</li>
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
                <a href="/services/lists/22/bp_edit/6/"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылка на БП
                </span>
                    <span class="badge bg-primary">
                    Ссылка на просмотр
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/services/lists/22/view/0/?list_section_id="
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылка на ИБ
                </span>
                    <span class="badge bg-success">
                    Ссылка на просмотр
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/crm/company/list/"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Компании
                </span>
                    <span class="badge bg-success">
                    Ссылка на просмотр
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=%2Flocal%2Factivities%2Fcustom%2Fcompanybyinnactivity%2Fcompanybyinnactivity.php&site=s1&lang=ru"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылка на активити
                </span>
                    <span class="badge bg-secondary">
                    файл в админке
                </span>
                </a>
            </li>

            
        </ul>
    </div>



<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>