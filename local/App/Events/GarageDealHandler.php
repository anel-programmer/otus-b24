<?php
namespace App\Events;

use Bitrix\Main\Loader;


class GarageDealHandler 
{
    /**
     * Событие сработывает перед добавлением сделки
     * проверяем есть ли открытая сделака для выбранного в сделке автомобиля
     */
    public static function OnBeforeCrmDealAdd(&$arFields) {
        \App\Debug\Log::addLog('OnBeforeCrmDealAdd');
        \App\Debug\Log::addLog($arFields);

        //проверка заполненно ли поле автобобиль
        $carId = (int)($arFields['UF_CRM_CAR_ID'] ?? 0);
        if ($carId <= 0) {
            // если эт
            return true;
        }

        $categoryId = (int)($arFields['CATEGORY_ID'] ?? 0);
        if ($categoryId <= 0) {
            return true;
        }

        $res = \Bitrix\Crm\DealTable::getList([
            'select' => ['ID', 'DATE_CREATE', 'ASSIGNED_BY_ID', 'CATEGORY_ID', 'TITLE', 'OPPORTUNITY', 'CURRENCY_ID', 'STAGE_ID', 'STAGE_SEMANTIC_ID'],
            'filter' => ['=UF_CRM_CAR_ID' => $arFields['UF_CRM_CAR_ID'], '=CATEGORY_ID' => $categoryId, 'CLOSED' => 'N'],
            'limit' => 1,
        ]);

        $deal = $res->fetch();
        
        if ($deal)
        {
            $arFields['RESULT_MESSAGE'] = 'По данному автомобилю есть не закрытая сделка. Создать вторую сделку пока не закрыта текущая невозможно!';

            //отправить уведомление
            if (Loader::includeModule('im')) {
                \CIMNotify::Add([
                    //получатель
                    'TO_USER_ID' => $deal['ASSIGNED_BY_ID'],
                    //отправитель
                    'FROM_USER_ID' => 0,
                    //тип уведомления.  IM_NOTIFY_SYSTEM - системное уведомление
                    'NOTIFY_TYPE' => IM_NOTIFY_SYSTEM, 
                    //модуль который запросил отправку уведомления
                    'NOTIFY_MODULE' => 'lukasheva.garage',
                    //текст уведомления
                    'NOTIFY_MESSAGE' => 'Сделка '.htmlspecialcharsbx($deal['TITLE']).' не закрыта. Новая сделка по автомобилю не возможна.',
                ]);
            }


            return false;
        }
        return true;

    }
}