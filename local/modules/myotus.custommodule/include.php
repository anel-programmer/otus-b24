<?php
use Bitrix\Main\Loader;

// Путь указывается относительно папки модуля (без /local/modules/...)
Loader::registerAutoLoadClasses('myotus.custommodule', [
    'Myotus\\Custommodule\\Orm\\MyclientsTable' => 'lib/orm/myclientstable.php',
]);