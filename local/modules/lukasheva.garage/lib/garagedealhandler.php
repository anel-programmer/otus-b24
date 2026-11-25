<?php
namespace Lukasheva\Garage;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;


class GarageDealHandler 
{
    /**
     * Событие сработывает перед добавлением сделки
     * проверяем есть ли открытая сделака для выбранного в сделке автомобиля
     */
    public static function OnBeforeCrmDealAdd(&$arFields) {

        //проверка заполненно ли поле автобобиль
        $carId = (int)($arFields['UF_CRM_CAR_ID'] ?? 0);
        if ($carId <= 0) {
            // если эт
            return true;
        }

        $categoryId = (int)($arFields['CATEGORY_ID'] ?? 0);
        $optionCategoryId = (int)Option::get('lukasheva.garage', 'category_id');
        if ($optionCategoryId <= 0 || $categoryId !== $optionCategoryId) {
            return true;
        }

        $res = \Bitrix\Crm\DealTable::getList([
            'select' => ['ID', 'DATE_CREATE', 'ASSIGNED_BY_ID', 'CATEGORY_ID', 'TITLE', 'OPPORTUNITY', 'CURRENCY_ID', 'STAGE_ID', 'STAGE_SEMANTIC_ID'],
            'filter' => ['=UF_CRM_CAR_ID' => $carId, '=CATEGORY_ID' => $optionCategoryId, 'CLOSED' => 'N'],
            'limit' => 1,
        ]);

        $deal = $res->fetch();
        
        if ($deal)
        {
            $arFields['RESULT_MESSAGE'] = Loc::getMessage('LUKASHEVA_GARAGE_DEAL_ADD_ERROR');

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
                    'NOTIFY_MESSAGE' =>  sprintf(Loc::getMessage('LUKASHEVA_GARAGE_DEAL_ADD_NOTIFY'), $deal['ID'], htmlspecialcharsbx($deal['TITLE'])),
                ]);
            }


            return false;
        }
        return true;

    }
}