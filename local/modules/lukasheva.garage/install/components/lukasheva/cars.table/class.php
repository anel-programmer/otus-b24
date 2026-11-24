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
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Validation\Rule\NotEmpty;
use Lukasheva\Garage\Orm\CarsTable as Cars;
use Lukasheva\Garage\Orm\ModelsTable as Models;
use Lukasheva\Garage\Orm\MakesTable as Makes;
use Bitrix\Main\Engine\CurrentUser;


class CarsTableComponent extends CBitrixComponent  implements Controllerable
{


    /* Controllerable 
    *  метод configureActions(): Этот метод обязателен для интерфейса. 
    * Он должен возвращать массив с конфигурацией доступных действий. 
    * Если специальная настройка не требуется, достаточно вернуть пустой массив: return [];
    */
    public function configureActions() {
        return [
            'saveAction' => [
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

       $arParams['CONTACT_ID'] = (int)($arParams['CONTACT_ID'] ?? 0);
       $arParams['NUM_PAGE'] = (int)($arParams['NUM_PAGE'] ?? 20) ?: 20;
       $arParams['SHOW_CHECKBOXES'] = ($arParams['SHOW_CHECKBOXES'] ?? 'N') === 'Y' ? 'Y' : 'N';
       return $arParams;
    }

    /**
     * Формирую список колонок грида
     * @return array
     */
    private function getColumn(): array
    {
        $columns = [];
        $fieldMap = Cars::getMap(); 
        $columns[] = [
            'id' => 'CAR_LINK',
            'name' => Loc::getMessage('CARS_TABLE_COLUMN_CAR_LINK'),
            'sort' => false,
            'default' => true,
        ];
        foreach ($fieldMap as $key => $field) {
            if ($field->getName()!='CONTACT_ID'){
                 $columns[] = array(
                    'id' => $field->getName(),
                    'name' => $field->getTitle()
                );
            }    
        }
        $columns[] = [
            'id' => 'CREATE_DEAL',
            'name' => Loc::getMessage('CARS_TABLE_COLUMN_NEWDEAL'),
            'sort' => false,
            'default' => true,
        ];
        
        return $columns;
    }

    /**
     * Получаю автомобили контакта с учётом постраничной навигации
     * и формирую строки для грида (данные, ссылка, действия).
     *
     * @param PageNavigation $nav        Объект постраничной навигации
     * @param int            $vContactID ID контакта-владельца
     *
     * @return array Строки грида
     */
    private function getList(PageNavigation $nav, int $vContactID)
    {

        $list = [];
        $data = Cars::getList([
            'select' => ['ID','CONTACT_ID','CARMAKE','MODEL','LICENSE_PLATE_NUMBER','YEAR_OF_MANUFACTURE','CAR_COLOR','MILEAGE'],
            'filter' => ['CONTACT_ID'=>$vContactID],
            'order' => ['ID' => 'ASC'],
            'limit' => $nav->getLimit(),
            'offset' => $nav->getOffset(),
        ]);
        
        while ($item = $data->fetch()) {
            $list[] = ['id' => $item['ID'],
                        'data' => $item,
                        'columns' => [
                            'CAR_LINK' => '<a href="#" onclick="BX.Lukasheva.Garage.view('
                                . (int)$item['ID'] . '); return false;">'
                                . htmlspecialcharsbx($item['CARMAKE']) . ' ' .htmlspecialcharsbx($item['MODEL']) .'</a>',
                            'CREATE_DEAL' => '<a href="#" onclick="BX.Lukasheva.Garage.newdeal('
                                . (int)$item['ID'] . '); return false;">'.Loc::getMessage('CARS_TABLE_ACTION_NEWDEAL').'</a>',
                        ],
                        'actions' => [
                            [
                                'text'=> Loc::getMessage('CARS_TABLE_ACTION_VIEW'),
                                'default' => true,
                                'onclick' => 'BX.Lukasheva.Garage.view(' . (int)$item['ID'] . ')',
                            ],
                            [
                                'text'=> Loc::getMessage('CARS_TABLE_ACTION_EDIT'),
                                'default' => false,
                                'onclick' => 'BX.Lukasheva.Garage.edit(' . (int)$item['ID'] . ')',
                            ],
                        ],
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
                throw new SystemException(Loc::getMessage('CARS_TABLE_NO_MODULE'));
            }
            
            $contactID = $this->arParams['CONTACT_ID'];
            $numPage = $this->arParams['NUM_PAGE'];

            if ($contactID <= 0) {
                throw new SystemException(Loc::getMessage('CARS_TABLE_NO_CONTACT'));
            }

           $permissions = Container::getInstance()->getUserPermissions();
           if (!$permissions->checkReadPermissions(\CCrmOwnerType::Contact, $contactID)) {
                // показать сообщение «нет доступа» и выйти
                throw new SystemException(Loc::getMessage('CARS_TABLE_NO_PERMISSIONS'));
            }
 
            $nav = new PageNavigation('report_list');
            $nav->allowAllRecords(false)->setPageSize($numPage)->initFromUri();

            $nav->setRecordCount(Cars::getCount(['CONTACT_ID'=>$contactID]));

            $this->arResult['NAV'] = $nav;
            $this->arResult['LISTS'] = $this->getList($nav, $contactID);
            $this->arResult['COUNT'] = $nav->getRecordCount();


            $this->arResult['SHOW_ROW_CHECKBOXES'] = false;

            if($this->arParams['SHOW_CHECKBOXES'] == 'Y'){
                $this->arResult['SHOW_ROW_CHECKBOXES'] = true;
            }

            $this->arResult['COLUMNS'] = $this->getColumn(); // получаем названия полей таблицы

            $this->IncludeComponentTemplate();

        }
        catch (SystemException $e)
        {
            ShowError($e->getMessage());
        }

    }

    /**
     * Получает сделки по автомобилю. 
     * Возвращает сделки на которые у пользователя есть права. 
     * 
     * @param int $carID - ID автомобиля
     * @return array
     */
    private function getDeals (int $carID):array {
        $permissions = Container::getInstance()->getUserPermissions();
        $categoryId = Option::get('lukasheva.garage', 'category_id');
        $stageNames = \Bitrix\Crm\Category\DealCategory::getStageList($categoryId);
        $deals = [];
        $listDeals = [];
        $res = \Bitrix\Crm\DealTable::getList([
            'select' => ['ID', 'DATE_CREATE', 'ASSIGNED_BY_ID', 'CATEGORY_ID', 'TITLE', 'OPPORTUNITY', 'CURRENCY_ID', 'STAGE_ID', 'STAGE_SEMANTIC_ID'],
            'filter' => ['=UF_CRM_CAR_ID' => $carID, '=CATEGORY_ID' => $categoryId],
            'order' => ['DATE_CREATE' => 'DESC'],
            'limit' => 100,
        ]);
        while ($row = $res->fetch()) {
            if ($permissions->checkReadPermissions(\CCrmOwnerType::Deal, $row['ID'])) {
                $deals[$row['ID']] = $row;
            }
        }

        //нет доступных сделок
        if (!$deals) {
            return [];
        }

        // получить данные ответственного
        $assigneds = [];
        $assignedIds = array_unique(array_column($deals, 'ASSIGNED_BY_ID'));
        $res = \Bitrix\Main\UserTable::getList([
            'select' => ['ID', 'NAME', 'LAST_NAME'],
            'filter' => ['@ID' => $assignedIds],
        ]);
        while ($assigned = $res->fetch()) {
            $assigneds[$assigned['ID']] = '<a href="/company/personal/user/'.$assigned['ID'].'/">'.htmlspecialcharsbx(trim($assigned['NAME'].' '.$assigned['LAST_NAME'])).'</a>';
        }

        //список запчастей
        $productsDeal = [];
        $res =  \Bitrix\Crm\ProductRowTable::getList([
            'filter' => ['=OWNER_TYPE' => 'D', '@OWNER_ID' => array_keys($deals)],
            'select' => ['OWNER_ID', 'PRODUCT_NAME', 'QUANTITY']
        ]);
        while ($product = $res->fetch()) {
            $productsDeal[$product['OWNER_ID']][] = [
                'PRODUCT_NAME' => htmlspecialcharsbx($product['PRODUCT_NAME']),
                'QUANTITY' => rtrim(rtrim((string)$product['QUANTITY'], '0'), '.')
                ];
        }

        foreach ($deals as  $dealId => $dealItem) {
            $listDeals[] = [
                    'DEAL_ID' => $dealId,
                    'DATE_CREATE' => Date::createFromTimestamp($dealItem['DATE_CREATE']->getTimestamp())->toString(),
                    'DEAL_TITLE' => '<a href="/crm/deal/details/'.$dealId.'/">'.htmlspecialcharsbx($dealItem['TITLE']).'</a>',
                    'STAGE' => $stageNames[$dealItem['STAGE_ID']] ?? $dealItem['STAGE_ID'],
                    'SUMMA' => \CCrmCurrency::MoneyToString($dealItem['OPPORTUNITY'], $dealItem['CURRENCY_ID']),
                    'STAGE_SEMANTIC_ID' => $dealItem['STAGE_SEMANTIC_ID'],
                    'ASSIGNED' => $assigneds[$dealItem['ASSIGNED_BY_ID']] ?? '',
                    'PRODUCTS' => $productsDeal[$dealId] ?? [],
            ];
        }
        return $listDeals;
    }

    /**
     * Ajax-действие: Просмотр данных автомобиля и связанных с ним сделок и запчастей. 
     * 
     * @param int $car_id - ID автомобиля
     * 
     * @return array
     */
    public function viewAction(int $car_id):array
    {


            // проверяем подключение модуля
            if (!Loader::includeModule('lukasheva.garage') || !Loader::includeModule('crm')) {
                // Если модуля нет, выбрасываем исключение, которое поймает catch ниже
                throw new SystemException(Loc::getMessage('CARS_TABLE_NO_MODULE'));
            }
            
            $item = Cars::getByPrimary($car_id, [
                'select' => ['*'], 
            ])->fetchObject();   

            if ($item === null) throw new SystemException(Loc::getMessage('CARS_TABLE_CAR_NOT_FOUND'));

            $permissions = Container::getInstance()->getUserPermissions();
            if (!$permissions->checkReadPermissions(\CCrmOwnerType::Contact, $item->getContactId())){
                 throw new SystemException(Loc::getMessage('CARS_TABLE_NO_PERMISSIONS'));
            }

           $contact = \Bitrix\Crm\ContactTable::getByPrimary($item->getContactId(), [
                'select' => ['NAME', 'LAST_NAME'],
            ])->fetch();
            $clientName = trim(($contact['NAME'] ?? '') . ' ' . ($contact['LAST_NAME'] ?? ''));
            $title = htmlspecialcharsbx($item->getCarmake() . ' ' . $item->getModel() . ' - ' . $item->getLicensePlateNumber() . ' (' . $clientName . ')');

            $rows = [
                'CARS_TABLE_LABEL_MODEL' => $item->getModel(),
                'CARS_TABLE_LABEL_CARMAKE' => $item->getCarmake(),
                'CARS_TABLE_LABEL_PLATE' => $item->getLicensePlateNumber(),
                'CARS_TABLE_LABEL_YEAR' => $item->getYearOfManufacture(),
                'CARS_TABLE_LABEL_COLOR' => $item->getCarColor(),
                'CARS_TABLE_LABEL_MILEAGE' => $item->getMileage(),
            ];

            $html = '<div class="car-info">';
            foreach ($rows as $labelCode => $value) {
                $html .= '<p><b>' . htmlspecialcharsbx(Loc::getMessage($labelCode)) . ':</b> '
                    . htmlspecialcharsbx((string)$value) . '</p>';
            }
            $html .= '</div>';

            $deals = $this->getDeals($car_id);
            
            if (count($deals)>0){ 

                $htmlDeals = '<h4><b>'.Loc::getMessage('CARS_TABLE_DEALS_TITLE').'</b></h4>
                    <div class="car-history-wrap">
                    <table class="car-history">
                        <thead>
                        <tr>
                            <th>'.Loc::getMessage('CARS_TABLE_DEAL_NAME').'</th>
                            <th>'.Loc::getMessage('CARS_TABLE_DEALS_DATE').'</th>
                            <th>'.Loc::getMessage('CARS_TABLE_DEALS_STATE').'</th>
                            <th>'.Loc::getMessage('CARS_TABLE_DEALS_ASSIGN').'</th>
                            <th>'.Loc::getMessage('CARS_TABLE_DEALS_SUMMA').'</th>
                            <th>'.Loc::getMessage('CARS_TABLE_DEALS_PRODUCTS').'</th>
                        </tr>
                        </thead>
                        <tbody>';


                foreach ($deals as $deal) {
                    $htmlProduct = '';
                    foreach ($deal['PRODUCTS'] as $product) {
                        $htmlProduct .= '<li>'.$product['PRODUCT_NAME'].'('.$product['QUANTITY'].')</li>';
                    }
                    if ($htmlProduct!='')
                        $htmlProduct = '<ul>'.$htmlProduct.'</ul>';

                    $htmlDeals .= '<tr>' 
                        .'<td>'.$deal['DEAL_TITLE'].'</td>'
                        .'<td>'. htmlspecialcharsbx($deal['DATE_CREATE']).'</td>'
                        .'<td>'. htmlspecialcharsbx($deal['STAGE']).'</td>'
                        .'<td>'. $deal['ASSIGNED'].'</td>'
                        .'<td>'. $deal['SUMMA'].'</td>'
                        .'<td>'.
                            $htmlProduct
                        .'</td>'
                        .'</tr>';
                }
                $htmlDeals .= '</tbody></table></div>';
            }
            else {
                $htmlDeals = '<h4>'.Loc::getMessage('CARS_TABLE_DEALS_NOT').'</h4>';
            }
            $html .= $htmlDeals;

            return ['title'=>$title, 'html'=>$html];
      
        
    }

     /**
     * Ajax-действие: Сохранение данных автомобиля. 
     * 
     * @param int $car_id - ID автомобиля
     * 
     * @return array
     */
    public function saveAction(array $fields, int $car_id, int $contact_id):array
    {

            // проверяем подключение модуля
            if (!Loader::includeModule('lukasheva.garage') || !Loader::includeModule('crm')) {
                // Если модуля нет, выбрасываем исключение, которое поймает catch ниже
                throw new SystemException(Loc::getMessage('CARS_TABLE_NO_MODULE'));
            }

            if ($car_id == 0) {
                //добавление новой записи
                $arrFields = [
                    'CARMAKE' => trim($fields['CARMAKE']) ?? '', 
                    'MODEL' => trim($fields['MODEL']) ?? '', 
                    'LICENSE_PLATE_NUMBER' => trim(mb_strtoupper($fields['LICENSE_PLATE_NUMBER'])) ?? '',
                    'YEAR_OF_MANUFACTURE' => trim($fields['YEAR_OF_MANUFACTURE']), 
                    'CAR_COLOR' => trim($fields['CAR_COLOR']) ?? '', 
                    'MILEAGE' => trim($fields['MILEAGE']) ?? '',
                    'CONTACT_ID' => $contact_id
                ];

                $result = Cars::add($arrFields);
               if (!$result->isSuccess()) {
                    throw new SystemException(implode('; ', $result->getErrorMessages()));
                }

                return ['id' => $result->getId()];
            }
            else {
                //редактирование существующей

            }

            return [];
    }


    /**
     * Создание сделки из гаража клиента
     * 
     * @param int $car_id - ID автомобиля
     */
    public function newdealAction(int $car_id): array
    {
         // проверяем подключение модуля
        if (!Loader::includeModule('lukasheva.garage') || !Loader::includeModule('crm')) {
            // Если модуля нет, выбрасываем исключение, которое поймает catch ниже
            throw new SystemException(Loc::getMessage('CARS_TABLE_NO_MODULE'));
        }
        $optionCategoryId = (int)Option::get('lukasheva.garage', 'category_id');
        //получить ID текущего пользователя 
        $userId = (int)CurrentUser::get()->getId();
        $item = Cars::getByPrimary($car_id, [
            'select' => ['*'], 
        ])->fetchObject();   

        if ($item === null) throw new SystemException(Loc::getMessage('CARS_TABLE_CAR_NOT_FOUND'));

        $permissions = Container::getInstance()->getUserPermissions();
        if (!$permissions->checkReadPermissions(\CCrmOwnerType::Contact, $item->getContactId())){
                throw new SystemException(Loc::getMessage('CARS_TABLE_NO_PERMISSIONS'));
        }

        /**
         * true, если нужно проверять права текущего пользователя.
         * Текущий пользователь определяется ID в ключе CURRENT_USER
         * $arOptions
         * @var boolean
         */
        $bCheckRight = true;

        /**
         * Добавляемая сущность
         * @var array
         */
        $entityFields = [

            'TITLE' => 'Сделка года',
            'STAGE_ID' => 'NEW',
            'PROBABILITY' => '100',
            'CURRENCY_ID' => 'RUB',
            'OPPORTUNITY' => 0,
            'COMPANY_ID' => 0,
            'CONTACT_IDS' => [
                $item->getContactId()
            ],
            'BEGINDATE' => '',
            'CLOSEDATE' => '',
            'OPENED' => 'Y',
            'COMMENTS' => "",

            'ASSIGNED_BY_ID' => $userId,
            'SOURCE_ID' => 'OTHER',
            'SOURCE_DESCRIPTION' => '',
            'CATEGORY_ID' => $optionCategoryId,
            'UF_CRM_CAR_ID' => $car_id,
        ];


        $entityObject = new \CCrmDeal($bCheckRight);

        $entityId = $entityObject->Add(
            $entityFields,
            $bUpdateSearch = true,
            $arOptions = [
                /**
                 * ID пользователя, от лица которого выполняется действие
                 * в том числе проверка прав
                 * @var integer
                 */
                'CURRENT_USER' => $userId,

                /**
                 * В случае true, битрикс создаст сообщение в ленту о создании
                 * @var boolean
                 */
                'REGISTER_SONET_EVENT' => true,

                /**
                 * В случае если флаг true не будут проверяться:
                 * - Пользовательские обязательные поля
                 * - Валидация пользовательских полей
                 * @var boolean
                 */
                'DISABLE_USER_FIELD_CHECK' => false,

                /**
                 * В случае если флаг true, не будет проверки
                 * обязательности заполнения пользовательских полей
                 *
                 * Если флаг 'DISABLE_USER_FIELD_CHECK' установлен в true,
                 * данный флаг игнорируется - проверок не будет
                 *
                 * Флаг не отменяет необходимость корректного заполнения
                 * переданных полей.
                 * @var boolean
                 */
                'DISABLE_REQUIRED_USER_FIELD_CHECK' => false,
            ]
        );

        if (!$entityId) {
            /**
             * Произошла ошибка при добавлении сущности, посмотреть ее можно
             * через любой из способов ниже:
             * 1. $entityFields['RESULT_MESSAGE']
             * 2. $entityObject->LAST_ERROR
             */
             throw new SystemException($entityObject->LAST_ERROR);

        }

        return ['id' => $entityId];
    }

     /**
     * Получаю названия моделей и марок автомобилей из стравочников
     *
     *
     * @return array 
     */
    public function dictionaryAction()
    {
          // проверяем подключение модуля
            if (!Loader::includeModule('lukasheva.garage')) {
                // Если модуля нет, выбрасываем исключение, которое поймает catch ниже
                throw new SystemException(Loc::getMessage('CARS_TABLE_NO_MODULE'));
            }
        
        $makes = [];
        $data = Makes::getList([
            'select' => ['*'],
            'order' => ['MAKE' => 'ASC'],
        ]);
        
        while ($item = $data->fetch()) {
            $makes[$item['ID']] = $item['MAKE'];
        }

        $models = [];
        $data = Models::getList([
            'select' => ['MODEL', 'MAKE_ID', 'MAKE' => 'MAKES.MAKE'],
            'order' => ['MODEL' => 'ASC'],
        ]);
        
        while ($item = $data->fetch()) {
            if (isset($makes[$item['MAKE_ID']])){
                $models[$item['MAKE']][] = $item['MODEL'];
            }
        }

        return ['makes'=>array_values($makes), 'models'=>$models];
    }



} 