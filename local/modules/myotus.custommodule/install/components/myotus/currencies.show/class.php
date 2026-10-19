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

// global $INTRANET_TOOLBAR;
// use Bitrix\Main\Engine\Contract\Controllerable;

use Bitrix\Main\Loader;
use Bitrix\Currency\CurrencyTable;
Loader::includeModule('currency');

class CurrenciesShowComponent extends \CBitrixComponent
{

    protected $request;

    /**
     * Подготовка параметров компонента
     * @param $arParams
     * @return mixed
    */
    public function onPrepareComponentParams($arParams) {
       // тут пишем логику обработки параметров, дополнение к параметрам по умолчанию
       return $arParams;
    }

    /**
     * Точка входа в компонент
     * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
     * всю логику стараемся разносить по классам и методам 
     */
    public function executeComponent() {

        try
        {

           // получаем параметры методов GET и POST, из обьекта request который позволяет получить данные о текущем запросе: метод и протокол, запрошенный URL, переданные параметры
           $this->arResult['CURENT_CURRENCY'] = 'USD';
           if (!empty($this->arParams['CURENT_CURRENCY'])) {
                $this->arResult['CURENT_CURRENCY'] = $this->arParams['CURENT_CURRENCY'];
           }

            $currencyData = CurrencyTable::getById($this->arResult['CURENT_CURRENCY'])->fetch();
            if ($currencyData) {
               $this->arResult['CURENT_CURRENCY_AMOUNT'] = $currencyData['AMOUNT'];
            } 

            // подключаем шаблон
            $this->IncludeComponentTemplate();
        }
        catch (SystemException $e)
        {
            ShowError($e->getMessage());
        }

    }


} 