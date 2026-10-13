<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Bizproc\Activity\BaseActivity;
use Bitrix\Bizproc\FieldType;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use Bitrix\Bizproc\Activity\PropertiesDialog;


class CBPCompanyByInnActivity extends BaseActivity
{
    // protected static $requiredModules = ["crm"];
    
    /**
     * @see parent::_construct()
     * @param $name string Activity name
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $this->arProperties = [
            'Inn' => '',

            // return
            'Text' => null,
        ];

        $this->SetPropertiesTypes([
            'Text' => ['Type' => FieldType::STRING],
        ]);
    }

    /**
     * Return activity file path
     * @return string
     */
    protected static function getFileName(): string
    {
        return __FILE__;
    }

    /**
     * @return ErrorCollection
     */
    protected function internalExecute(): ErrorCollection 
    {
        $errors = parent::internalExecute(); 

        /*$token = "56512809ef9c58d8c5cd7e453bb1f11ec4044e5a";
        $secret = "2a47cf7f1f9fe36365b75ee39291e594ed27e973";
        */

        // token и secret передается в виде переменных БП в активити
        $rootActivity = $this->GetRootActivity();
        $token = $rootActivity->GetVariable("TOKEN"); 
        $secret =  $rootActivity->GetVariable("SECRET"); 
        
        $dadata = new Dadata($token, $secret);
        $dadata->init();

        $fields = array("query" => $this->Inn, "count" => 5);
        $response = $dadata->suggest("party", $fields);
        
        $companyName = 'Компания не найдена!';
        if(!empty($response['suggestions'])){ // если копания найдена
           // по ИНН возвращается массив в котором может бытьнесколько элементов (компаний)
           $companyName = $response['suggestions'][0]['value']; // получаем имя компании из первого элемента  

           //создаем компанию
           \Bitrix\Main\Loader::includeModule('crm');
           //id ответственного и уведомителя
            $responsible = 1;
           
            $arNewCompany = array(
                "TITLE" =>  $companyName,
                "OPENED" => "Y",
                "COMPANY_TYPE" => "CUSTOMER",
                "ASSIGNED_BY_ID" => $responsible,
            );
           
            $company = new CCrmCompany(false);
            $companyID = $company->Add($arNewCompany);
            
            if (!$companyID){
                $this->log('Произошла ошибка при добавлении компании:'.' '.$company->LAST_ERROR);
            }
            else {
                $this->log('Значение поля ID Добавленной компании:'.' '.$companyID);
                $this->preparedProperties['Text'] = $companyID;
            }
        }  

        return $errors;
    }

    /**
     * @param PropertiesDialog|null $dialog
     * @return array[]
     */
    public static function getPropertiesDialogMap(?PropertiesDialog $dialog = null): array
    {
        $map = [
            'Inn' => [
                'Name' => Loc::getMessage('SEARCHBYINN_ACTIVITY_FIELD_SUBJECT'),
                'FieldName' => 'inn',
                'Type' => FieldType::STRING,
                'Required' => true,
                'Options' => [],
            ],
        ];
        return $map;
    }




}