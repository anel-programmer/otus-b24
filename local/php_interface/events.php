<?php
use Bitrix\Main\Application;
use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Bitrix\Main\Page\Asset;

$eventManager = EventManager::getInstance();
$asset = Asset::getInstance();

/**
 * Подключение события которое выполняется после обновления записи в ИБ Заявки
 */
 $eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockElementUpdate',
    ['App\Events\IblockHandler', 'onElementAfterUpdate']
); 

/**
 * Подключение события которое выполняется после обновления Сделки в CRM
 */
  $eventManager->addEventHandler(
    'crm',
    'OnAfterCrmDealUpdate',
    ['App\Events\DealHandler','onCrmDealAfterUpdate']
);
/**
 * обработчик, который добавляет пустую вкладку "custom"
 */
 /*$eventManager->addEventHandler(
    'crm',
    'onEntityDetailsTabsInitialized',
    ['App\Events\TabHandler','setCustomTabs']
);*/

/**
 * Подключение события которое кастомный тип поля рейтинг
 */
 $eventManager->addEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    ['App\UserTypes\CIBlockPropertyRating','GetUserTypeDescription']
);

$eventManager->addEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    ['App\UserTypes\CIBlockPropertyReservation','GetUserTypeDescription']
);

$eventManager->addEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    ['App\UserTypes\IBLink','GetUserTypeDescription']
);

// подключение JS модуля из пространства otus, который находится в \local\js\
// модуль otus.console демо, который выводит в консоль фразу  Hi, from Otus.
//\Bitrix\main\UI\Extension::load('otus.console');

// подключение JS модуля из пространства otus, который находится в \local\js\
// модуль otus.console демо, который выводит в консоль фразу  Hi, from Otus.
//\Bitrix\main\UI\Extension::load('otus.workday');

