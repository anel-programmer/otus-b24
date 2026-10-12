<?php
namespace App\Events;

/**
 * метод обращается к менеджеру табов и возвращает объект класса EventResult, который содержит данные по актуальным вкладкам
 */
use Bitrix\Main\EventResult; 
use Bitrix\Main\Event;
use Bitrix\Main\Loader;
Loader::includeModule('crm');

class TabHandler 
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
                //$tabs = $this->getActualDealTabs($tabs, $entityId);
                $tabs[] = [
                    'id' => 'component_users',
                    'name' => 'Custom Tab',
                    'enabled' => !empty($entityId),
                    'loader' => [
                        'serviceUrl' => '/local/components/otus/currencies.show/lazyload.ajax.php?&site=' . \SITE_ID . '&' . \bitrix_sessid_get(),
                        'componentData' => [
                            'template' => '.default',
                            'params' => [
                                // Параметры вызываемого компонента ($arParams)
                                "CURENT_CURRENCY" => "RUB"
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