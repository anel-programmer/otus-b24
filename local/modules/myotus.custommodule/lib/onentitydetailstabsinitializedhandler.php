<?php
namespace Myotus\Custommodule;

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
    public static function setCustomTabs(Event $event): EventResult
    {
        $entityId = $event->getParameter('entityID');
        $entityTypeID = $event->getParameter('entityTypeID');
        $tabs = $event->getParameter('tabs');

        switch ($entityTypeID) {
            case \CCrmOwnerType::Deal:
                $tabs[] = [
                    'id' => 'component_currencies',
                    'name' => Loc::getMessage('OTUS_CURRENCY_TAB'),
                    'enabled' => !empty($entityId),
                    'loader' => [
                        'serviceUrl' => '/local/components/myotus/currencies.show/lazyload.ajax.php?&site=' . \SITE_ID . '&' . \bitrix_sessid_get(),
                        'componentData' => [
                            'template' => '.default',
                            'params' => [
                                // Параметры вызываемого компонента ($arParams)
                                "CURENT_CURRENCY" => "RUB"
                            ]
                        ]
                    ]
                ];
                $tabs[] = [
                    'id' => 'component_table',
                    'name' => Loc::getMessage('OTUS_TABLE_TAB'),
                    'enabled' => !empty($entityId),
                    'loader' => [
                        'serviceUrl' => '/local/components/myotus/client.table/lazyload.ajax.php?&site=' . \SITE_ID . '&' . \bitrix_sessid_get(),
                        'componentData' => [
                            'template' => 'list',
                            'params' => [
                                // Параметры вызываемого компонента ($arParams)
                                "SHOW_CHECKBOXES" => "Y",
                                "NUM_PAGE" => "3"
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
                break;
        }


        /*$crmCustomTabManager = new CrmCustomTabManager();

        $tabs = $crmCustomTabManager->getActualEntityTab($entityId, $entityTypeID, $tabs);*/

        return new EventResult(EventResult::SUCCESS, [
            'tabs' => $tabs,
        ]);
    }
}