<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

/** @global CIntranetToolbar $INTRANET_TOOLBAR */

use Bitrix\Main\Application;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Error;


class ReservationFieldComponent extends CBitrixComponent  implements Controllerable
{

    protected $request;

    /**
     * Подготовка параметров компонента
     * @param $arParams
     * @return mixed
    */
  /*   public function onPrepareComponentParams($arParams) {
       // тут пишем логику обработки параметров, дополнение к параметрам по умолчанию
       return $arParams;
    } */

    /**
     * Точка входа в компонент
     * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
     * всю логику стараемся разносить по классам и методам 
     */
    public function executeComponent() {
         try
        {
            // подключаем шаблон
            $this->IncludeComponentTemplate();

        }
        catch (SystemException $e)
        {
            ShowError($e->getMessage());
        }
      
    }

    /* Controllerable 
    *  метод configureActions(): Этот метод обязателен для интерфейса. 
    * Он должен возвращать массив с конфигурацией доступных действий. 
    * Если специальная настройка не требуется, достаточно вернуть пустой массив: return [];
    */
    public function configureActions() {
        return [];
    }

    // Сюда передаются параметры из ajax запроса, навания точно такие же как и при отправке запроса.
    public function addAction(array $fields)
    {
        if (CModule::IncludeModule('iblock')){
            $arFields = array(
                "ACTIVE" => "Y", 
                "IBLOCK_ID" =>  $fields['iblock_id'],
                "IBLOCK_SECTION_ID" => false,
                "NAME" => $fields['fio'],
                "PROPERTY_VALUES" => array(
                    "FIO_PATIENT" => $fields['fio'], 
                    "BOOKING_TIME" => $fields['datetime'], 
                    "PROCEDURE" => $fields['procedure_id'] 
                )
            );
            $oElement = new \CIBlockElement();
            if($idElement = $oElement->Add($arFields, false, false, true))
                return ['id' => $idElement];
            else
                \App\Debug\Log::addLog($oElement->LAST_ERROR);
        }
        
    }
} 