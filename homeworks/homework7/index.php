<?

use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("ДЗ #7: Создание кастомных полей и встраивание их в систему");

Asset::getInstance()->addCss('//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');


?>
<h1 class="mb-3"><? $APPLICATION->ShowTitle() ?></h1>

<h4 class="mb-3">Пояснительная записка</h4>
<div>
    <div style="font-style: italic;">
       <ol>
        <li>Создала список Бронирование с полями: ФИО пациента, Время записи, Процедура</li>
        <li>Создала компонент otus:reservation.field который выводит список процедур, при клике на процедуру, открывает popup окно с полями ФИО пациента и дата и время.
            После нажатия на кнопку "Записать" в этом окне добавляет запись с ФИО пациента и выбранным временем а список "Бронирование".
        </li>
        <li>Добавила кастомное свойство "Бронирование записи" в котором в метод отображения значения в списке в публичной части подключила компонент otus:reservation.field,
            в методах редактирования значения в списке в публичной части, а также при добавлении и редактировании в админке сделала выпадающие списки для выбора процедур.
            В настройках кастомного свойства добавила списки с выбором типа инфоблока и инфоблока, для того чтоб можно было указать инфоблок откуда должны загружаться процедуры, 
            и инфоблок - куда должны записываться данные о новой брони. 
        </li>
       </ol>
    </div>
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
                <a href="/services/lists/23/view/0/"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылка на список бронирование
                </span>
                    <span class="badge bg-warning">
                    Ссылка на просмотр
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/services/lists/16/view/0/"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылка на список врачей
                </span>
                    <span class="badge bg-warning">
                    Ссылка на просмотр
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=%2Flocal%2FApp%2FUserTypes%2FCIBlockPropertyReservation.php&site=s1&lang=ru"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылки на просмотр кода файла с описанием кастомного типа свойства (Бронирование записи) 
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_admin.php?lang=ru&path=%2Flocal%2Fcomponents%2Fotus%2Freservation.field&site=s1"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Компонент otus:reservation.field который подключается в методе отображения значения в списке в публичной части
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>
           
        </ul>
    </div>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>