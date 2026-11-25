<?php

namespace Lukasheva\Garage\Agent;

use	Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Catalog\ProductTable;
use Lukasheva\Garage\Orm\ZajavkyTable;
use Lukasheva\Garage\Orm\DetailTable;
use Bitrix\Main\Config\Option;
use Bitrix\Main\UserGroupTable;

Loc::loadMessages(__FILE__);

class UpdateQuantity
{
    public static function update(): string
    {
        try {
            //получить ID каталога
            $catalog_id = \Bitrix\Main\Config\Option::get('crm', 'default_product_catalog_id'); 
                    
            $options = [
                "socketTimeout" => 10, // Таймаут соединения, сек
                "streamTimeout" => 20, // Таймаут чтения ответа, сек
            ];
            $url = 'https://www.random.org/integers/?num=1&min=0&max=10&col=1&base=10&format=plain&rnd=new';
            $httpClient = new HttpClient($options);

            $result = ProductTable::getList([
                'select' => [
                    'ID',
                    'NAME' => 'IBLOCK_ELEMENT.NAME',
                    'TYPE',
                ],
                'filter' => [
                    '=IBLOCK_ELEMENT.IBLOCK_ID' => $catalog_id,
                    'IBLOCK_ELEMENT.ACTIVE' => 'Y',
                    '=IBLOCK_ELEMENT.IBLOCK_SECTION_ID' => 12,
                    '=TYPE' => ProductTable::TYPE_PRODUCT,
                ],
            ]);
            while ($product = $result->fetch()) {
                try {
                    $randomCount = $httpClient->get($url);
                    if ($randomCount === false || $httpClient->getStatus() !== 200) {
                        //внешний сервис вернул felse или не отвечает, переходим к следующей записи, эту запись оставляем не обновленной
                        continue;
                    }
                    $randomCount = trim($randomCount);
                    \App\Debug\Log::addLog(Loc::getMessage('LUKASHEVA_GARAGE_AUTO_NEW_QUANTITY',
                        [
                            '#NAME#' => $product['NAME'],
                            '#QUANTITY#' => $randomCount,
                        ]));
                    if ($randomCount >= 0 && $randomCount <= 10) {
                        $res = ProductTable::update($product['ID'], [
                            'QUANTITY' => (int)$randomCount
                        ]);
                        if (!$res->isSuccess()) {
                             \App\Debug\Log::addLog(Loc::getMessage('LUKASHEVA_GARAGE_AUTO_NOT_UPDATE',
                                [
                                    '#NAME#' => $product['NAME'],
                                ]));
                            continue;
                        }
                        if ((int)$randomCount == 0) {
                            //создаем автоматическую заявку на покупку
                            \App\Debug\Log::addLog(Loc::getMessage('LUKASHEVA_GARAGE_AUTO_CREATE_ZAJAVKA',
                                [
                                    '#NAME#' => $product['NAME'],
                                ]));
                            //сервис автозаявки: при нуле создать заявку на 10 штук, выставить остаток 10, статус «Выполнено», уведомить закупщика.
                            self::createAutoZajavka($product);
                        }
                    }
                } catch (\Throwable $e){
                    \App\Debug\Log::addLog(Loc::getMessage('LUKASHEVA_GARAGE_AUTO_ERROR_UPDATE',
                        [
                            '#ID#' => $product['ID'],
                            '#ERROR#' => $e->getMessage(),
                        ]));
                }
            }
        } catch (\Throwable $e) {
            \App\Debug\Log::addLog(Loc::getMessage('LUKASHEVA_GARAGE_AUTO_ERROR',
                        [
                            '#ERROR#' => $e->getMessage(),
                        ]));
        }

        return 'Lukasheva\Garage\Agent\UpdateQuantity::update();';
    }

    private static function createAutoZajavka(array $aProduct): void
    {
        try {
            $defaultQuantity = 10;
            $statusZajavky = ZajavkyTable::STATUS_NEW;

            $arFieldsZajavky = [
                'TYPE_ZAJAVKY' => ZajavkyTable::TYPE_AUTO,
                'CREATED_BY_ID' => 0,
                'DEAL_ID' => 0,
            ];
        
            $resZajavky = ZajavkyTable::add($arFieldsZajavky);
            if ($resZajavky->isSuccess()) {
                $arFieldsDetail = [
                    'ZAJAVKA_ID' => $resZajavky->getId(),
                    'PRODUCT_ID' => $aProduct['ID'],
                    'QUANTITY' => $defaultQuantity,
                    
                ];
                $resDetail = DetailTable::add($arFieldsDetail);
                if (!$resDetail->isSuccess()) {
                    \App\Debug\Log::addLog((implode('; ', $resDetail->getErrorMessages())));
                }
                else {
                    //заявка создана обновляем количество товара в каталоге и отправляем уведомление закупщику
                    $res = ProductTable::update($aProduct['ID'], [
                            'QUANTITY' => $defaultQuantity
                    ]);
                    if (!$res->isSuccess()) {
                        \App\Debug\Log::addLog(Loc::getMessage('LUKASHEVA_GARAGE_AUTO_NOT_UPDATE',
                            [
                                '#NAME#' => $aProduct['NAME'],
                            ]));
                        $statusZajavky = ZajavkyTable::STATUS_REJECTED;
                        //оставляем статус заявки новая и отправляем закупщику сообщение что автоматически заявка не выполнилась
                    }
                    else {
                         \App\Debug\Log::addLog(Loc::getMessage('LUKASHEVA_GARAGE_AUTO_SUCCESS_UPDATE',
                            [
                                '#NAME#' => $aProduct['NAME'],
                                '#QTY#' => $defaultQuantity,
                            ]));
                        $statusZajavky = ZajavkyTable::STATUS_DONE;
                        //изменить статус у заявки
                        $res = ZajavkyTable::update($resZajavky->getId(), ['STATUS_ZAJAVKY' => $statusZajavky]);
                        if (!$res->isSuccess()) {
                            \App\Debug\Log::addLog(Loc::getMessage('LUKASHEVA_GARAGE_AUTO_STATUS_NOT_UPDATE',
                                [
                                    '#ID#' => $resZajavky->getId(),
                                ]));
                        }
                    }
                    self::sendNotify($aProduct, $statusZajavky, $defaultQuantity);

                }
            }
            else {
                 \App\Debug\Log::addLog(Loc::getMessage('LUKASHEVA_GARAGE_AUTO_CREATE_ZAJAVKA_ERROR',
                    [
                        '#ERROR#' => implode('; ',$resZajavky->getErrorMessages()),
                    ]));
                 return;
            }
        } catch (\Throwable $e) {
             \App\Debug\Log::addLog(Loc::getMessage('LUKASHEVA_GARAGE_AGENT_ERROR',
                [
                    '#ERROR#' => $e->getMessage(),
                ]));
        }
    }

    private static function sendNotify(array $product, string $statusZajavky, int $defaultQuantity): void {

        $groupId = Option::get('lukasheva.garage', 'group_id');

        if (Loader::includeModule('im')) {
            //получить список пользователей группы 
            $rows = UserGroupTable::getList([
                'select' => ['USER_ID'],
                'filter' => [
                    '=GROUP_ID' => $groupId,
                    '=USER.ACTIVE' => 'Y',
                ],
            ]);

            while ($aUser = $rows->fetch()){
                //отправить уведомление
                    \CIMNotify::Add([
                        //получатель
                        'TO_USER_ID' => $aUser['USER_ID'],
                        //отправитель
                        'FROM_USER_ID' => 0,
                        //тип уведомления.  IM_NOTIFY_SYSTEM - системное уведомление
                        'NOTIFY_TYPE' => IM_NOTIFY_SYSTEM, 
                        //модуль который запросил отправку уведомления
                        'NOTIFY_MODULE' => 'lukasheva.garage',
                        //текст уведомления
                        'NOTIFY_MESSAGE' =>  Loc::getMessage('LUKASHEVA_GARAGE_AUTO_'.$statusZajavky.'_NOTIFY',
                            [ 
                                '#NAME#' => htmlspecialcharsbx($product['NAME']),
                                '#QTY#' => $defaultQuantity]),
                    ]);
            }
        }
    }
}