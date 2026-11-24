<?php
use Bitrix\Main\Loader;

// Путь указывается относительно папки модуля (без /local/modules/...)
Loader::registerAutoLoadClasses('lukasheva.garage', [
    'Lukasheva\\Garage\\Orm\\CarsTable' => 'lib/orm/carstable.php',
    'Lukasheva\\Garage\\Orm\\MakesTable' => 'lib/orm/makestable.php',
    'Lukasheva\\Garage\\Orm\\ModelsTable' => 'lib/orm/modelstable.php',
    'Lukasheva\\Garage\\Orm\\ZajavkyTable' => 'lib/orm/zajavkytable.php',
    'Lukasheva\\Garage\\Orm\\DetailTable' => 'lib/orm/detailtable.php',
    'Lukasheva\\Garage\\Service\\DealPartsService' => '/lib/service/dealpartsservice.php',
]);