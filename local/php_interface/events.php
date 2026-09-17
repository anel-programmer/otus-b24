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

