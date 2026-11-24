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

use Bitrix\Main\Loader;
use	Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Crm\Service\Container;
use Bitrix\Main\Engine\Contract\Controllerable;
use Lukasheva\Garage\Orm\ZajavkyTable;
use Lukasheva\Garage\Service\DealPartsService;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Engine\ActionFilter;

class GaragePartsComponent extends CBitrixComponent  implements Controllerable
{


    /* Controllerable 
    *  метод configureActions(): Этот метод обязателен для интерфейса. 
    * Он должен возвращать массив с конфигурацией доступных действий. 
    * Если специальная настройка не требуется, достаточно вернуть пустой массив: return [];
    */
    public function configureActions() {
        return [
            'createRequestAction' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_POST]),
                    new ActionFilter\Authentication(),
                ],
            ],
        ];
    } 
    /**
     * Подготовка параметров компонента
     * @param $arParams
     * @return mixed
    */
    public function onPrepareComponentParams($arParams) {

       $arParams['DEAL_ID'] = (int)($arParams['DEAL_ID'] ?? 0);
       return $arParams;
    }

    /**
     * Формирую список колонок грида
     * @return array
     */
    private function getColumn(): array
    {
        $columns = [];

        $columns[] = [
            'id' => 'PRODUCT_ID',
            'name' => Loc::getMessage('GARAGE_PARTS_COLUMN_PRODUCT_ID'),
            'sort' => false,
            'default' => true,
        ];
        $columns[] = [
            'id' => 'PRODUCT_NAME',
            'name' => Loc::getMessage('GARAGE_PARTS_COLUMN_PRODUCT_NAME'),
            'sort' => false,
            'default' => true,
        ];
        $columns[] = [
            'id' => 'QUANTITY_NEED',
            'name' => Loc::getMessage('GARAGE_PARTS_COLUMN_QUANTITY_NEED'),
            'sort' => false,
            'default' => true,
        ];
        $columns[] = [
            'id' => 'QUANTITY_STOCK',
            'name' => Loc::getMessage('GARAGE_PARTS_COLUMN_QUANTITY_STOCK'),
            'sort' => false,
            'default' => true,
        ];
        $columns[] = [
            'id' => 'QUANTITY_BUY',
            'name' => Loc::getMessage('GARAGE_PARTS_COLUMN_QUANTITY_BUY'),
            'sort' => false,
            'default' => true,
        ];
                 
        return $columns;
    }

    private function getRecordCount(array $prList): int
    {
        return sizeof($prList);
    }

    private function getRows(array $prList): array
    {
        $list = [];
        foreach($prList as $item){
            $list[] = [
                'id' => $item['PRODUCT_ID'],
                'data' => $item,             
            ];
        }
        return $list;
    }

    /**
     * Точка входа в компонент
     * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
     * всю логику стараемся разносить по классам и методам 
     */
    public function executeComponent() {

        try
        {

            // проверяем подключение модуля
            if (!Loader::includeModule('lukasheva.garage') || !Loader::includeModule('crm')) {
                // Если модуля нет, выбрасываем исключение, которое поймает catch ниже
                throw new SystemException(Loc::getMessage('GARAGE_PARTS_NO_MODULE'));
            }
            
            $dealID = $this->arParams['DEAL_ID'];

            if ($dealID <= 0) {
                throw new SystemException(Loc::getMessage('GARAGE_PARTS_NO_DEAL'));
            }

           $permissions = Container::getInstance()->getUserPermissions();
           if (!$permissions->checkReadPermissions(\CCrmOwnerType::Deal, $dealID)) {
                // показать сообщение «нет доступа» и выйти
                throw new SystemException(Loc::getMessage('GARAGE_PARTS_NO_PERMISSIONS'));
            }
            
            $this->arResult['DEAL_ID'] = $dealID;

            $productList = DealPartsService::getShortage($dealID);

            $this->arResult['COLUMNS'] = $this->getColumn();
            $this->arResult['LISTS'] = $this->getRows($productList);
            $this->arResult['COUNT'] = $this->getRecordCount($productList);
 
            $this->IncludeComponentTemplate();

        }
        catch (SystemException $e)
        {
            ShowError($e->getMessage());
        }

    }

    /**
     * Ajax-действие: Ручное создание заявки на закупку запчастей. 
     * 
     * @param int $product_ids - ID запчастей
     * 
     * @param int $deal_id - ID сделки
     * 
     * @return int
     */
    public function createRequestAction(array $product_ids, int $deal_id):int
    {

        // проверяем подключение модуля
        if (!Loader::includeModule('lukasheva.garage') || !Loader::includeModule('crm')) {
            // Если модуля нет, выбрасываем исключение, которое поймает catch ниже
            throw new SystemException(Loc::getMessage('GARAGE_PARTS_NO_MODULE'));
        }

        //проверка прав на сделку
        $permissions = Container::getInstance()->getUserPermissions();
        if (!$permissions->checkUpdatePermissions(\CCrmOwnerType::Deal, $deal_id)) {
            throw new SystemException(Loc::getMessage('GARAGE_PARTS_NO_PERMISSIONS'));
        }

        //получить ID текущего пользователя 
        $userId = (int)CurrentUser::get()->getId();

        $product_ids = array_values(array_filter(array_map('intval', $product_ids)));
        $productList = DealPartsService::getShortage($deal_id);

        $productsBuy = [];
        foreach ($productList as $product){
            if (in_array($product['PRODUCT_ID'],$product_ids) && ($product['QUANTITY_BUY'] > 0)){
                $productsBuy[] = [
                    'ID' => $product['PRODUCT_ID'],
                    'QUANTITY_BUY' => $product['QUANTITY_BUY'],
                ];
            }
        }

        if (!$productsBuy){
            throw new SystemException(Loc::getMessage('GARAGE_PARTS_NO_PARTS'));
        }

        //проверка на дубль
        if (DealPartsService::issetDuble($deal_id)){
             throw new SystemException(Loc::getMessage('GARAGE_PARTS_REQUEST_DUBLE'));
        }

        //перед созданием заявки проверка есть ли шаблон БД и список согласующих
        $id_BP = (int)Option::get('lukasheva.garage', 'bp_template_id');
        if ($id_BP == 0) {
             throw new SystemException(Loc::getMessage('GARAGE_PARTS_NO_BP'));
        }
        // Список ID согласующих
        $approverIds = DealPartsService::getApproversIdByGroup((int)Option::get('lukasheva.garage', 'group_id'));

        $zajavky_id = DealPartsService::createZajavka($productsBuy, $deal_id, $userId, ZajavkyTable::TYPE_MANUAL);

        if ($zajavky_id <= 0) {
            throw new SystemException(Loc::getMessage('GARAGE_PARTS_REQUEST_CREATE_ERROR'));
        }

        $this->runBizProc($deal_id, $zajavky_id, $userId, $id_BP, $approverIds);

        return $zajavky_id;
    }

    /**
     *  Запуск бизнес-процесса
     * 
     *  @param int $dealID - ID сделки
     * 
     *  @return void
     */
    private static function runBizProc(int $dealID, int $zajavkaID, int $userId, int $id_BP, array $approverIds): void
    {

        // проверяем подключение модуля
        if (!Loader::includeModule('lukasheva.garage') || !Loader::includeModule('bizproc')) {
            // Если модуля нет, выбрасываем исключение, которое поймает catch ниже
            throw new SystemException(Loc::getMessage('GARAGE_PARTS_NO_MODULE'));
        }

        $arErrors = [];
        // Формируем массив в формате Битрикс БП ('user_ID')
        $formattedApprovers = array_map(
            static fn(int $id): string => 'user_' . $id,
            $approverIds
        );

        $arParameters = [
            'REQUEST_ID' => $zajavkaID,
            'APPROVERS' => $formattedApprovers, //список согласующих
            'TARGETUSER' =>  'user_' .$userId, // Кто запустил процесс
        ];
        $documentId = ['crm', 'CCrmDocumentDeal', 'DEAL_'.$dealID];

        $bzProc = CBPDocument::StartWorkflow($id_BP, $documentId, $arParameters, $arErrors);
        $errorMessage = '';
        if ((count($arErrors) > 0) || is_null($bzProc)) {
	        foreach ($arErrors as $e)
		        $errorMessage .= "[".$e["code"]."] ".$e["message"]."    ";
            \App\Debug\Log::addLog($errorMessage);
            throw new SystemException(Loc::getMessage('GARAGE_PARTS_ERROR_BP'));
        }

        return;
    }

    
} 