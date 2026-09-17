<?php
namespace App\Events;

use Bitrix\Main\Loader;


class DealHandler 
{
    /**
     * Событие сработывает после обновления сделки
     * изменяет свойства суммы и ответственный в Списке "Заявки"
     */
    public static function onCrmDealAfterUpdate(&$arFields) {
        \App\Debug\Log::addLog('onCrmDealAfterUpdate');
        \App\Debug\Log::addLog($arFields);

        // редактирование элемента в инфоблоке Заявки
        \Bitrix\Main\Loader::IncludeModule("iblock");
        //зная символьный код API ИБ Заявки Applications через D7 получает ID элемента и IBLOCK_ID 
        $elements = \Bitrix\Iblock\Elements\ElementApplicationsTable::getList([ 
            'select' => ['ID', 'IBLOCK_ID', 'SDELKA_'=>'SDELKA', 'SUMMA_'=>'SUMMA', 'OTVETSTVENNYY_'=>'OTVETSTVENNYY'], // имя свойства 
            'filter' => ['SDELKA_VALUE'=>$arFields['ID']], //фильтруем по ID сделки из $arFields
        ])->fetch();
        \App\Debug\Log::addLog($elements);
        //проверяем была ли изменена сумма сделки или ее ответственный, если да то записываем новое значение в $newValues
        $newValues = [];
        if ($elements['SUMMA_VALUE']!=$arFields['OPPORTUNITY']){
             $newValues['SUMMA'] = $arFields['OPPORTUNITY'];
        }
        if ($elements['OTVETSTVENNYY_VALUE']!=$arFields['ASSIGNED_BY_ID']){
             $newValues['OTVETSTVENNYY'] = $arFields['ASSIGNED_BY_ID'];
        }

        if (isset($elements['ID']) && !empty($newValues)) {
            // делаем запрос на изменение свойства SUMMA в записи с ID = $elements['ID'] с помощью классического API SetPropertyValuesEx. 
            // метод update D7  работает только с полями
            \CIBlockElement::SetPropertyValuesEx($elements['ID'], $elements['IBLOCK_ID'], $newValues);
            \App\Debug\Log::addLog('update');
        }

    }
}