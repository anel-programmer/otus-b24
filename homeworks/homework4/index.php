<?

use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("ДЗ #4: Создание своих таблиц БД и написание модели данных к ним");

Asset::getInstance()->addCss('//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');

?>
    <h1 class="mb-3"><? $APPLICATION->ShowTitle() ?></h1>

    <h4 class="mb-3">Пояснительная записка</h4>
    <div>
       <ol>
        <li>Создала модель для кастомной таблицы НomeworkTable.php</li>
        <li>Создала 2 инфоблока "Издательства" и "Авторы книг"</li>
        <li>Создала модели для инфоблоков MypublisherTable.php (Издательства) и AuthorsTable.php
 (Авторы книг)</li>
        <li>Связала инфоблоки с кастомной таблицей с помощью Reference в getMap() модели кастомной таблицы</li>
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
                <a href="/bitrix/admin/perfmon_table.php?lang=ru&table_name=homework_four"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылка на таблицу homework_four
                </span>
                    <span class="badge bg-success">
                   Ссылка на просмотр в админке
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=19&type=lists&lang=ru&find_section_section=0&SECTION_ID=0&apply_filter=Y"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Список на ИБ 1 (Списки => Издательства)
                </span>
                    <span class="badge bg-primary">
                   Ссылка на просмотр в админке
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=18&type=lists&lang=ru&find_section_section=0&SECTION_ID=0&apply_filter=Y"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Список на ИБ 2 (Списки => Авторы книг)
                </span>
                    <span class="badge bg-primary">
                   Ссылка на просмотр в админке
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="/custom_orm/index.php"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылка на тестовую страницу
                </span>
                    <span class="badge bg-secondary">
                   Ссылка на просмотр
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?lang=ru&site=s1&path=%2Fcustom_orm%2Findex.php"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылка на тестовую страницу
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_admin.php?lang=ru&path=%2Flocal%2FApp%2FModels%2FHomework4&site=s1"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылки на просмотр кода основных файлов ДЗ (связь таблиц, ORM, классы  и т.д.)
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>
        </ul>
    </div>





<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>