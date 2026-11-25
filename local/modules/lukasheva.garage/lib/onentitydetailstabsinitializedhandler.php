<?php
namespace Lukasheva\Garage;

/**
 * метод обращается к менеджеру табов и возвращает объект класса EventResult, который содержит данные по актуальным вкладкам
 */
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\EventResult; 
use Bitrix\Main\Event;
use Bitrix\Main\Loader;
Loader::includeModule('crm');

class onEntityDetailsTabsInitializedHandler 
{
    /**
     * Получение актуальных вкладок элемента CRM
     * @param Event $event
     * @return EventResult
     */
    public static function setGarageTab(Event $event): EventResult
    {
        $entityId = $event->getParameter('entityID');
        $entityTypeID = $event->getParameter('entityTypeID');
        $tabs = $event->getParameter('tabs');

        switch ($entityTypeID) {
            case \CCrmOwnerType::Deal:
                // @TODO Реализовать получение вкладок для сделки
                $serviceUrl = '/local/components/lukasheva/garage.parts/lazyload.ajax.php?' . http_build_query([
                    'site' => \SITE_ID,
                    'tpl' => '',
                    'deal_id' => $entityId,
                ]) . '&' . \bitrix_sessid_get();
                 $tabs[] = [
                    'id' => 'garage_parts',
                    'name' => Loc::getMessage('GARAGE_PARTS_TABLE_TAB'),
                    'enabled' => !empty($entityId),
                    'loader' => [
                        'serviceUrl' => $serviceUrl,
                        'componentData' => [
                            'template' => '',
                            'params' => [
                                // Параметры вызываемого компонента ($arParams)
                                "DEAL_ID" =>  $entityId,
                            ]
                        ]
                    ]
                ];
                break;
            case \CCrmOwnerType::Company:
                // @TODO Реализовать получение вкладок для компаний
                break;
            case \CCrmOwnerType::Contact:
                // @TODO Реализовать получение вкладок для контактов
               
                $serviceUrl = '/local/components/lukasheva/cars.table/lazyload.ajax.php?' . http_build_query([
                    'site' => \SITE_ID,
                    'tpl' => 'list',
                    'num_page' => 3,
                    'show_checkboxes' => 'Y',
                    'contact_id' => $entityId,
                ]) . '&' . \bitrix_sessid_get();
                $tabs[] = [
                    'id' => 'component_table',
                    'name' => Loc::getMessage('GARAGE_TABLE_TAB'),
                    'enabled' => !empty($entityId),
                    'loader' => [
                        'serviceUrl' => $serviceUrl,
                        'componentData' => [
                            'template' => 'list',
                            'params' => [
                                // Параметры вызываемого компонента ($arParams)
                                "SHOW_CHECKBOXES" => "Y",
                                "NUM_PAGE" => "3",
                                "CONTACT_ID" =>  $entityId,
                            ]
                        ]
                    ]
                ];
                break;
        }


        return new EventResult(EventResult::SUCCESS, [
            'tabs' => $tabs,
        ]);
    }
}