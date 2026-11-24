<?php
namespace Lukasheva\Garage\Service;

use Bitrix\Main\Loader;
use	Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Catalog\ProductTable;
use Bitrix\Crm\ProductRowTable;
use Bitrix\Main\Config\Option;
use Lukasheva\Garage\Orm\ZajavkyTable;
use Lukasheva\Garage\Orm\DetailTable;
use Bitrix\Main\UserGroupTable;
use Bitrix\Main\Result;
use Bitrix\Main\Error;

Loc::loadMessages(__FILE__);

class DealPartsService 
{
     /**
     * Получает количество товара на складе, в сделке и сколько нужно докупить
     * 
     * @param int $dealId - ID сделки
     * @return array
     */
    public static function getShortage (int $dealId):array {

        // проверяем подключение модуля
        if (!Loader::includeModule('crm') || !Loader::includeModule('catalog')) {
            // Если модуля нет, выбрасываем исключение, которое поймает catch ниже
            throw new SystemException(Loc::getMessage('GARAGE_SERVICE_NO_MODULE'));
        }

        $catalog_id = (int)Option::get('crm', 'default_product_catalog_id'); 

        //список запчастей
        $productsDeal = [];
        $res =  ProductRowTable::getList([
            'filter' => ['=OWNER_TYPE' => 'D', '=OWNER_ID' => $dealId],
            'select' => ['PRODUCT_ID', 'PRODUCT_NAME', 'QUANTITY']
        ]);
        while ($product = $res->fetch()) {
            if ((int)$product['PRODUCT_ID'] > 0) {
                $productsDeal[] = [
                    'PRODUCT_ID' => $product['PRODUCT_ID'],
                    'PRODUCT_NAME' => $product['PRODUCT_NAME'],
                    'QUANTITY' => $product['QUANTITY'],
                    ];
            }
        }

        if (count($productsDeal) <= 0){
			return [];
		}

        $listProducts = [];
        foreach ($productsDeal as  $product) {
            $key = $product['PRODUCT_ID'];
            if (!isset($listProducts[$key])) {
                $listProducts[$key] = [
                    'PRODUCT_ID' => $product['PRODUCT_ID'],
                    'PRODUCT_NAME' => $product['PRODUCT_NAME'],
                    'QUANTITY_NEED' => 0,
                    'QUANTITY_BUY' => 0,
                    'QUANTITY_STOCK' => 0
                ];
            }
            $listProducts[$key]['QUANTITY_NEED'] += (int)$product['QUANTITY']; 
            
        }

        //список запчастей и актуальное количество к каталоге

        $result = ProductTable::getList([
            'select' => [
                'ID',
                'QUANTITY'
            ],
            'filter' => [
                '=IBLOCK_ELEMENT.IBLOCK_ID' => $catalog_id,
                'IBLOCK_ELEMENT.ACTIVE' => 'Y',
                '=IBLOCK_ELEMENT.IBLOCK_SECTION_ID' => 12,
                '=TYPE' => ProductTable::TYPE_PRODUCT,
                '@ID' => array_keys($listProducts)
            ],
        ]);
        
        while ($product = $result->fetch()) {
            $quantityStock = (int)$product['QUANTITY'];
            if ($listProducts[$product['ID']]['QUANTITY_NEED'] > $quantityStock){
                $quantityBuy = $listProducts[$product['ID']]['QUANTITY_NEED'] -  $quantityStock;
                $listProducts[$product['ID']]['QUANTITY_BUY'] = $quantityBuy;
            }
            $listProducts[$product['ID']]['QUANTITY_STOCK'] = $quantityStock;
            
        }
        return $listProducts;
    }

    public static function issetDuble($dealId): bool
    {
        $resIssetZajavky = ZajavkyTable::getList([
            'select' => ['ID'],
            'filter' => [
                'DEAL_ID' => $dealId,
                '=TYPE_ZAJAVKY' => ZajavkyTable::TYPE_MANUAL,
                'STATUS_ZAJAVKY' => [ZajavkyTable::STATUS_NEW]
            ]
        ]);

        if ($resIssetZajavky->fetch()) {
            return true;
        }
        return false;
    }

    /**
     * Создание ручной заявки на закупку запчастей
     * 
     */
    public static function createZajavka(array $aProduct, int $dealId = 0, int $createdById = 0, string $typeZajavky = ZajavkyTable::TYPE_MANUAL): int
    {
        try {

            $arFieldsZajavky = [
                'TYPE_ZAJAVKY' => $typeZajavky,
                'CREATED_BY_ID' => $createdById,
                'DEAL_ID' => $dealId,
            ];
        
            $resZajavky = ZajavkyTable::add($arFieldsZajavky);
            if ($resZajavky->isSuccess()) {
                foreach ($aProduct as $item){
                    $arFieldsDetail = [
                        'ZAJAVKA_ID' => $resZajavky->getId(),
                        'PRODUCT_ID' => $item['ID'],
                        'QUANTITY' => $item['QUANTITY_BUY'],
                    ];
                    \App\Debug\Log::addLog($arFieldsDetail);
                    $resDetail = DetailTable::add($arFieldsDetail);
                    if (!$resDetail->isSuccess()) {
                        \App\Debug\Log::addLog((implode('; ', $resDetail->getErrorMessages())));
                        return 0;
                    }
                }
                return $resZajavky->getId();
            }
            else {
                 \App\Debug\Log::addLog(Loc::getMessage('LUKASHEVA_GARAGE_AUTO_CREATE_ZAJAVKA_ERROR',
                    [
                        '#ERROR#' => implode('; ',$resZajavky->getErrorMessages()),
                    ]));
                 return 0;
            }
        } catch (\Throwable $e) {
             \App\Debug\Log::addLog(Loc::getMessage('LUKASHEVA_GARAGE_AGENT_ERROR',
                [
                    '#ERROR#' => $e->getMessage(),
                ]));
            return 0;
        }
    }

    /**
     * Получить список ID пользователей группы для согласующих
     * 
     */
    public static function getApproversIdByGroup(int $groupId):array
    {
        
        // проверяем подключение модуля
        if (!Loader::includeModule('intranet')) {
            // Если модуля нет, выбрасываем исключение, которое поймает catch ниже
            throw new SystemException(Loc::getMessage('GARAGE_SERVICE_NO_MODULE'));
        }

        $userIDs = [];
        $res = UserGroupTable::getList([
            'select' => ['USER_ID'],
            'filter' => [
                '=GROUP_ID' => $groupId,
                '=USER.ACTIVE' => 'Y',
            ],
        ]);
        while ($aUser = $res->fetch()){
            if (!\CIntranetUtils::IsUserAbsent((int)$aUser['USER_ID'])) {
                $userIDs[] = (int)$aUser['USER_ID'];
            }
        }

        if (!$userIDs){
            $userIDHead = (int)Option::get('lukasheva.garage', 'head_id');
            if ($userIDHead != 0)
                $userIDs[] = $userIDHead;
        }

        return $userIDs;

    }
    
    /**
     * Согласование закупки через бизнес-процесс
     */
    public static function approveRequest(int $requestId) : Result
    {
        $result = new Result();
        \App\Debug\Log::addLog('approveRequest '.$requestId);
        try {
            // проверяем подключение модуля
            if (!Loader::includeModule('crm') || !Loader::includeModule('catalog')) {
                // Если модуля нет, выбрасываем исключение, которое поймает catch ниже
                $result->addError(new Error(Loc::getMessage('GARAGE_SERVICE_NO_MODULE')));
                \App\Debug\Log::addLog(Loc::getMessage('GARAGE_SERVICE_NO_MODULE'));
                return $result;
            }
            //проверка статуса заявки
            $resZajavka = ZajavkyTable::getByPrimary($requestId)->fetch();
            if (!$resZajavka){
                $result->addError(new Error(Loc::getMessage('GARAGE_SERVICE_NOFOUND_ZAJAVKA')));
                \App\Debug\Log::addLog(Loc::getMessage('GARAGE_SERVICE_NOFOUND_ZAJAVKA'));
                return $result;
            }
            if ($resZajavka['STATUS_ZAJAVKY'] != ZajavkyTable::STATUS_NEW) {
                $result->addError(new Error(Loc::getMessage('GARAGE_SERVICE_NO_NEW_ZAJAVKA')));
                \App\Debug\Log::addLog(Loc::getMessage('GARAGE_SERVICE_NO_NEW_ZAJAVKA'));
                return $result;
            }

            $productsBuy = [];
            $resDetail = DetailTable::getList([
                'select' => ['*'],
                'filter' => [
                    '=ZAJAVKA_ID' => $requestId
                ],
            ]);
            while ($product = $resDetail->fetch()){
                $productsBuy[$product['PRODUCT_ID']] = $product['QUANTITY'];
            }

            foreach ($productsBuy as $key => $value){
                $currentProduct = ProductTable::getByPrimary($key)->fetch();
                if (!$currentProduct) {
                    $result->addError(new Error(Loc::getMessage('GARAGE_SERVICE_NOFOUND_PRODUCT')));
                    \App\Debug\Log::addLog(Loc::getMessage('GARAGE_SERVICE_NOFOUND_PRODUCT'));
                    return $result;
                }

                $newQuantity = (int)$value + (int)$currentProduct['QUANTITY'];

                $resUpdate = ProductTable::update($key, [
                    'QUANTITY' => $newQuantity
                ]);
                if (!$resUpdate->isSuccess()) {
                    $result->addError(new Error(Loc::getMessage('GARAGE_SERVICE_NOUPDATE_PRODUCT',
                        [
                            '#PRODUCT_ID#' => $key,
                            '#REQUEST_ID#' => $requestId,
                        ])));
                    \App\Debug\Log::addLog(Loc::getMessage('GARAGE_SERVICE_NOUPDATE_PRODUCT',
                        [
                            '#PRODUCT_ID#' => $key,
                            '#REQUEST_ID#' => $requestId,
                        ]));
                    return $result;
                }
            }
                
            $statusZajavky = ZajavkyTable::STATUS_DONE;
            //изменить статус у заявки
            $resStatus = ZajavkyTable::update($requestId, ['STATUS_ZAJAVKY' => $statusZajavky]);
            if (!$resStatus->isSuccess()) {
                $result->addError(new Error(Loc::getMessage('GARAGE_SERVICE_STATUS_NOT_UPDATE',
                    [
                        '#REQUEST_ID#' => $requestId,
                    ])));
                return $result;
            }
            \App\Debug\Log::addLog(Loc::getMessage('GARAGE_SERVICE_SUCCESS_UPDATE',
                [
                    '#REQUEST_ID#' => $requestId
                   
                ]));
            return $result;
                
        }
        catch (\Throwable $e)
        {
            $result->addError(new Error(Loc::getMessage('GARAGE_SERVICE_UPDATE_ERROR', ['#ERROR#' => $e->getMessage()])));
            \App\Debug\Log::addLog(Loc::getMessage('GARAGE_SERVICE_UPDATE_ERROR', ['#ERROR#' => $e->getMessage()]));
            return $result;
        }
    }

     /**
     * Отклонение закупки через бизнес-процесс
     * устанавливает статус - Отклонено для заявки
     * 
     */
    public static function rejectRequest(int $requestId) : Result
    {
        $result = new Result();
        \App\Debug\Log::addLog('rejectRequest '.$requestId);
        try {
           
            //проверка статуса заявки
            $resZajavka = ZajavkyTable::getByPrimary($requestId)->fetch();
            if (!$resZajavka){
                $result->addError(new Error(Loc::getMessage('GARAGE_SERVICE_NOFOUND_ZAJAVKA')));
                \App\Debug\Log::addLog(Loc::getMessage('GARAGE_SERVICE_NOFOUND_ZAJAVKA'));
                return $result;
            }
            if ($resZajavka['STATUS_ZAJAVKY'] != ZajavkyTable::STATUS_NEW) {
                $result->addError(new Error(Loc::getMessage('GARAGE_SERVICE_NO_NEW_ZAJAVKA')));
                \App\Debug\Log::addLog(Loc::getMessage('GARAGE_SERVICE_NO_NEW_ZAJAVKA'));
                return $result;
            }

                  
            $statusZajavky = ZajavkyTable::STATUS_REJECTED;
            //изменить статус у заявки
            $resStatus = ZajavkyTable::update($requestId, ['STATUS_ZAJAVKY' => $statusZajavky]);
            if (!$resStatus->isSuccess()) {
                $result->addError(new Error(Loc::getMessage('GARAGE_SERVICE_STATUS_NOT_UPDATE',
                    [
                        '#REQUEST_ID#' => $requestId,
                    ])));
                return $result;
            }
            \App\Debug\Log::addLog(Loc::getMessage('GARAGE_SERVICE_REJECTED',
                [
                    '#REQUEST_ID#' => $requestId
                   
                ]));
            return $result;
                
        }
        catch (\Throwable $e)
        {
            $result->addError(new Error(Loc::getMessage('GARAGE_SERVICE_UPDATE_ERROR', ['#ERROR#' => $e->getMessage()])));
            \App\Debug\Log::addLog(Loc::getMessage('GARAGE_SERVICE_UPDATE_ERROR', ['#ERROR#' => $e->getMessage()]));
            return $result;
        }
    }


}