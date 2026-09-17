<?

use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("ДЗ #10: Обработка событий");

Asset::getInstance()->addCss('//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');


?>
<h1 class="mb-3"><? $APPLICATION->ShowTitle() ?></h1>

<h4 class="mb-3">Пояснительная записка</h4>
<div>
    <ol>
        <li>Создала ИБ Заявки. В нем свойства. Сделка с типом "Привязка к элементам CRM", Ответственный - тип "Привязка к сотрудникам", Сумма - тип "Деньги"</li>
        <li>Создала файл events.php для того чтоб именно в нем размещать подключения событий и не захломлять init.php</li>
        <li>Создала каталог App\Events и в нем 2 класса IblockHandler (с методом изменения суммы и ответственного в привязанной сделке) и DealHandler (с методом изменения суммы и ответственного после обновления сделки)</li>
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
                <a href="/services/lists/20/view/0/"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Ссылка на ИБ Заявки
                </span>
                    <span class="badge bg-primary">
                    Ссылка на просмотр
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/crm/deal/category/0/"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Сделки
                </span>
                    <span class="badge bg-warning">
                    Ссылка на просмотр
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=%2Flocal%2FApp%2FEvents%2FDealHandler.php&site=s1&lang=ru"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Файл с используемыми событиями Сделки: OnAfterCrmDealUpdate - App\Events\DealHandler::onCrmDealAfterUpdate
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=%2Flocal%2FApp%2FEvents%2FIblockHandler.php&site=s1&lang=ru"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Файл с используемыми событиями Заявки: OnAfterIBlockElementUpdate - App\Events\IblockHandler::onElementAfterUpdate
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>

            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=%2Flocal%2Fphp_interface%2Fevents.php&site=s1&lang=ru"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    events.php файл в котором подключаются события
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>
        </ul>
    </div>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>