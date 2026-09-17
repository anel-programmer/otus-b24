<?php
namespace App\Events;

use Bitrix\Main\Loader;
use Bitrix\Crm;

class IblockHandler 
{
    /**
     * Событие сработывает после обновления записи в ИБ "Заявки"
     * изменяет свойства суммы и ответственный CRM привязанной сделаки
     */
    public static function onElementAfterUpdate(&$arFields) {
        \App\Debug\Log::addLog('onElementAfterUpdate');
        \App\Debug\Log::addLog($arFields);

        //Проверяем если редактируется запись не из ИБ Заявки то прекращаем выполнение события
        if ($arFields['IBLOCK_ID']!=20){
            return $arFields;
        }
        if (Loader::includeModule('iblock')) {
            $iblockId  = $arFields['IBLOCK_ID'];
            $dbProps = \CIBlockProperty::GetList(
                [], 
                ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y']
            );
            $tmpFields = [];
            while ($prop = $dbProps->Fetch()) {
               
                if (isset($arFields['PROPERTY_VALUES'][$prop['ID']]))
                {
                    \App\Debug\Log::addLog($arFields['PROPERTY_VALUES'][$prop['ID']]); 
                    $propValues = $arFields['PROPERTY_VALUES'][$prop['ID']];
                    if (is_array($propValues)) {
                        foreach ($propValues as $keyItem => $valueItem) {
                            $tmpFields[$prop['CODE']] = $valueItem['VALUE'];
                        }
                    } else {
                        $tmpFields[$prop['CODE']] = $propValues;
                    }
                    
                }

            }
            \App\Debug\Log::addLog($tmpFields); 
        }

        // обновление сделаки, старое API (https://hmarketing.ru/blog/bitrix24/metody-dlya-raboty-s-sdelkami/)
        if (\Bitrix\Main\Loader::IncludeModule('crm') && !empty($tmpFields))
		{
            $dealID = $tmpFields['SDELKA']; // ID обновляемой сделки
            $vFields = []; 
            if (isset($tmpFields['SUMMA'])){
                $tmpSum = explode('|',$tmpFields['SUMMA']);
                if ($tmpSum[0])
                    $vFields['OPPORTUNITY'] = $tmpSum[0];
            }
            $vFields['ASSIGNED_BY_ID'] = $tmpFields['OTVETSTVENNYY'];

            if (!empty($vFields)) {
                $CCrmDeal = new \CCrmDeal(false);
                
                if ($CCrmDeal->Update($dealID, $vFields)) {
                   \App\Debug\Log::addLog('Сделка успешно обновлена! '.$dealID);
                } else {
                    \App\Debug\Log::addLog('Ошибка обновления: ' . $CCrmDeal->LAST_ERROR);
                }
            }
		}
        
        

    }
}