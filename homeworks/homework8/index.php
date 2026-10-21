<?

use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("ДЗ #8: Учимся подключать свои скрипты, взаимодействовать с компонентами из фронтенда");

Asset::getInstance()->addCss('//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');


?>
<h1 class="mb-3"><? $APPLICATION->ShowTitle() ?></h1>

<h4 class="mb-3">Пояснительная записка</h4>
<div>
    <ol>
        <li>Нашла событие, которое срабатывает при открытии окна с таймером. Это событие <b>onTimeManDataRecieved</b>, один из парамметров, который она возвращает 
            STATE может принимать значения OPENED | CLOSED | PAUSED | EXPIRED . 
            Эти значение и буду использовать в дальнейшем, чтоб понимать начинать или продолжать рабочий день.
        </li>
        <li>
            Написала JS расширение <b>otus.workday</b>, в котором отлавливается событие onTimeManDataRecieved. 
            Далее штатную кнопку "Начать рабочий день" скрыла и на ее место добавила свою, при нажатии на которую открывается popup окно 
            с уточняющим вопросом о уверенности начать рабочий день. 
        </li>
        <li>
            Запуск таймера реализовала на стороне php в контроллере <b>workday.php</b> А обращение в контроллер выполняю с помощью BX.ajax.runAction('otus:workday.workday.start'...)
            После успешного запуска таймера, сам таймер не обновляется, для этого пришлось сделать перезагрузку страницы.
        </li>
        <li>
            Констроллер и JS расширение добавила в свой модуль <b>otus.workday</b> чтоб данный функционал не был привязан к одному порталу и представлял собой самостоятельное решение. 
        </li>
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
                <a href="/bitrix/admin/fileman_admin.php?PAGEN_1=1&SIZEN_1=20&lang=ru&site=s1&path=%2Flocal%2Fmodules%2Fotus.workday&show_perms_for=0&fu_action="
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Директория модуля otus.workday
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=%2Flocal%2Fmodules%2Fotus.workday%2Finstall%2Fjs%2Fotus%2Fworkday%2Fscript.js&site=s1&lang=ru"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    script.js JS-расширения в котором реализован основной функционал для фронтенда 
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=%2Flocal%2Fmodules%2Fotus.workday%2Flib%2Fcontroller%2Fworkday.php&site=s1&lang=ru"
                   class="d-flex justify-content-between align-items-center">
                <span>
                   Контроллер workday.php в котором запускается таймер
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_edit.php?path=%2Flocal%2Fmodules%2Fotus.workday%2Flib%2Feventhandler.php&full_src=Y&site=s1&lang=ru&&filter=Y&set_filter=Y"
                   class="d-flex justify-content-between align-items-center">
                <span>
                   eventhandler.php подключение JS-расширения в событи onBeforeProlog
                </span>
                    <span class="badge bg-warning">
                    файл в админке
                </span>
                </a>
            </li>
        </ul>
    </div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>