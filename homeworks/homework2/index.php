<?

use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("ДЗ #2: Отладка и логирование");

Asset::getInstance()->addCss('//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');

?>
    <h1 class="mb-3"><? $APPLICATION->ShowTitle() ?></h1>

    <h4 class="mb-3">Пояснительная записка</h4>
    <div style="color: #797778;font-style: italic;">
       <h5>Часть 1 - Logger</h5>
       <ol>
        <li>Создала класс <a href="/bitrix/admin/fileman_file_view.php?path=/local/App/Debug/Log.php" target="_blank">App\Debug\Log</a> 
        унаследованный от класса FileExceptionHandlerLog</li>
        <li>Добавила в него метод addLog, который создает кастомный лог файл в каталоге /local/logs/, если файла с указанным именем не существует. 
            Или дописывает текущую дату, время и сообщение в существующий файл лога. 
        </li>
        <li>Добавила в класс метод cleanLog, который проверяет существование лог файла с указанным именем, 
            и если такой файл существует, то очищает его содержимое.</li>
        <li>Создала в каталоге /local/App/ фаил <a href="/bitrix/admin/fileman_file_view.php?path=/local/App/autoload.php" target="_blank">autoload.php</a>,
            в котором с помощью функции <a href="https://www.php.net/manual/ru/function.spl-autoload-register.php" target="_blank">spl_autoload_register</a> 
            написала код, подключающий автоматически все классы из каталога /local/App/</li>
        <li>Подключила файл /local/App/autoloader.php в /local/php_interface/init.php</li>
        <li>Добавила вызовы соответствующих методов к файлы <a href="writelog.php" target="_blank">writelog.php</a> и <a href="clearlog.php" target="_blank">clearlog.php</a></li>
       </ol>
        <h5>Часть 2 - Exception</h5>
       <ol>
        <li>В ранне созданном классе <a href="/bitrix/admin/fileman_file_view.php?path=/local/App/Debug/Log.php" target="_blank">App\Debug\Log</a> 
        переопределила метод write, в котором в строку создания форматированного $message в начало добавила слово "OTUS"</li>
        <li>В local создала файл <a href="/bitrix/admin/fileman_file_view.php?path=/local/.settings_extra.php" target="_blank">.settings_extra.php</a>,
        в котором изменила парраметры для exception_handling, а именно имя класса, обрабатывающего exception, 
        указала на \App\Debug\Log и изменила имя и расположение лога для exception.
        </li>
        <li>Добавила вызовы соответствующих методов к файлы <a href="writeexception.php" target="_blank">writeexception.php</a>
         и <a href="clearexception.php" target="_blank">clearexception.php</a></li>
       </ol>
    <br>
    <br>
    <hr>

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-success text-white">
            Файлы проекта: Часть 1 - Logger
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=local/logs/log_custom.log"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    local/logs/log_custom.log
                </span>
                    <span class="badge bg-success">
                    Файл лога из п1 ДЗ
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="writelog.php"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    writelog.php
                </span>
                    <span class="badge bg-secondary">
                    Добавление в лог из п1 ДЗ
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="clearlog.php"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    clearlog.php
                </span>
                    <span class="badge bg-warning">
                    Очистить лог из п1 ДЗ
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=/local/App/Debug/Log.php"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Файл с классом кастомного логгера
                </span>
                    <span class="badge bg-primary">
                    класс логгера в админке
                </span>
                </a>
            </li>

        </ul>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-success text-white">
            Файлы проекта: Часть 2 - Exception
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=local/logs/exceptions.log"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    local/logs/exceptions.log
                </span>
                    <span class="badge bg-primary">
                    Файл лога из п2 ДЗ
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="writeexception.php"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    writeexception.php
                </span>
                    <span class="badge bg-success">
                    Добавление в лог из п2 ДЗ
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="clearexception.php"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    clearexception.php
                </span>
                    <span class="badge bg-secondary">
                    Очистить лог из п2 ДЗ
                </span>
                </a>
            </li>
            <li class="list-group-item list-group-item-action">
                <a href="/bitrix/admin/fileman_file_view.php?path=/local/App/Debug/Log.php"
                   class="d-flex justify-content-between align-items-center">
                <span>
                    Файл с классом системного исключений
                </span>
                    <span class="badge bg-warning">
                    класс логгера в админке
                </span>
                </a>
            </li>
        </ul>
    </div>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>