<?

use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
/** @global $APPLICATION */
$APPLICATION->SetTitle("ДЗ #3: Связывание моделей");

Asset::getInstance()->addCss('//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');

?>
    <h1 class="mb-3"><? $APPLICATION->ShowTitle() ?></h1>

    <h4 class="mb-3">Пояснительная записка</h4>
    <div style="font-style: italic;">
       <ol>
        <li>Создала списки Врачи и Процедуры в админке. У списка Врачи создала необходимые свойства.</li>
        <li>Создала классы DoctorsPropertyValuesTable и  ProcsPropertyValuesTable
        унаследованный от абстрактного класса AbstractIblockPropertyValuesTable (этот класс рассматривали на уроке и рекомендовали использовать его в ДЗ).</li>
        <li>В одном исполняемом файле /doctors/index.php написала приложение, которое выводит список врачей, процедур. Позволяет добавлять врача и процедуру. 
            Редактировать и просматривать данные врача. В качестве перехода между действиями использовале get запросы. </li>
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
                <a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=16&type=lists&lang=ru&find_section_section=0&SECTION_ID=0&apply_filter=Y"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Список врачей
                </span>
                    <span class="badge bg-primary">
                   Ссылка на просмотр
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=17&type=lists&lang=ru&find_section_section=0&SECTION_ID=0&apply_filter=Y"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Список процедур
                </span>
                    <span class="badge bg-success">
                   Ссылка на просмотр
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="/doctors/"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Врачи и процедуры
                </span>
                    <span class="badge bg-secondary">
                   Ссылка на просмотр
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=%2Flocal%2FApp%2FModels%2FLists%2FDoctorsPropertyValuesTable.php&site=s1&lang=ru"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Класс DoctorsPropertyValuesTable для работы со списком Врачи
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=%2Flocal%2FApp%2FModels%2FLists%2FProcsPropertyValuesTable.php&site=s1&lang=ru"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Класс ProcsPropertyValuesTable для работы со списком Процедуры
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=%2Flocal%2FApp%2FModels%2FAbstractIblockPropertyValuesTable.php&site=s1&lang=ru"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Абстрактный класс AbstractIblockPropertyValuesTable унаследованный от DataManager с модифицированными методама
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>
        </ul>
    </div>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>