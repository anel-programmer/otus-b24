<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

class lukasheva_garage extends CModule
{
    public $MODULE_ID = 'lukasheva.garage';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME = 'Лукашева Елена';
    public $PARTNER_URI = '';

    public function __construct()
    {
        include __DIR__ . '/version.php';

        if (isset($arModuleVersion['VERSION'], $arModuleVersion['VERSION_DATE']))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = Loc::getMessage('LUKASHEVA_INSTALL_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('LUKASHEVA_MODULE_DESCRIPTION');
        
    }

    public function DoUninstall(): void
	{
		global $APPLICATION, $step, $USER;

		if (!$USER->IsAdmin())
		{
			return;
		}

		$step = (int)($step ?? $_REQUEST['step'] ?? 0);
        
		if ($step < 2)
		{
  
			$APPLICATION->IncludeAdminFile(
				Loc::getMessage('LUKASHEVA_UNINSTALL_TITLE'),
				__DIR__. '/unstep1.php',
			);
		}
		elseif ($step === 2)
		{
            $save_field = $_REQUEST['save_field'] ?? 'N';
            if ($save_field !== 'Y') {
                $entity = new \CUserTypeEntity();

                $exists = \CUserTypeEntity::GetList([], [
                    'ENTITY_ID' => 'CRM_DEAL',
                    'FIELD_NAME' => 'UF_CRM_CAR_ID',
                ])->Fetch();
                if ($exists) {
                    $entity->Delete((int)$exists['ID']);
                }
            }
            
			$this->UnInstallDB([
				'save_tables' => $_REQUEST['save_tables'] ?? 'N',
			]);
            $this->UnInstallEvents();
			$this->UnInstallFiles();

            //удаление всех агентов модуля
            \CAgent::RemoveModuleAgents('lukasheva.garage');            

			$APPLICATION->IncludeAdminFile(
				Loc::getMessage('LUKASHEVA_UNINSTALL_TITLE'),
				__DIR__ . '/unstep2.php',
			);
		}
	}
    
    public function DoInstall()
    {
        global $USER, $APPLICATION;

         if (!check_bitrix_sessid())
        {
            return false;
        }

        if (!$USER->IsAdmin())
        {
            return;
        }

        ModuleManager::registerModule($this->MODULE_ID);

        $this->InstallEvents();
        $this->InstallDB();
        $this->InstallFiles();

        //Создание поня ID Автомобиля в Сделке, для связи с кодом из гаража
        $entity = new \CUserTypeEntity();

        $exists = \CUserTypeEntity::GetList([], [
            'ENTITY_ID' => 'CRM_DEAL',
            'FIELD_NAME' => 'UF_CRM_CAR_ID',
        ])->Fetch();

        if (!$exists) {
            $label = Loc::getMessage('GARAGE_UF_CAR_ID');
            $entity->Add([
                'ENTITY_ID' => 'CRM_DEAL',
                'FIELD_NAME' => 'UF_CRM_CAR_ID',
                'USER_TYPE_ID' => 'integer',
                'XML_ID' => 'GARAGE_CAR_ID',
                'SORT' => 100,
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'I',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'EDIT_FORM_LABEL' => ['ru' => $label, 'en' => $label],
                'LIST_COLUMN_LABEL' => ['ru' => $label, 'en' => $label],
                'LIST_FILTER_LABEL' => ['ru' => $label, 'en' => $label],
            ]);
        }

        $res = \CAgent::GetList([],['NAME' => 'Lukasheva\Garage\Agent\UpdateQuantity::update();']);
        $exists = $res->fetch();
        if (!$exists) {
            //создание агента UpdateQuantity если не существует
            \CAgent::AddAgent(
                '\\Lukasheva\\Garage\\Agent\\UpdateQuantity::update();', // что вызывать
                'lukasheva.garage',                               // модуль (подключится перед запуском)
                'N',                                         // следующий запуск будет через интервал после завершения прошлого
                86400,                                       // интервал в секундах (сутки)
                '',                                          // дата первой проверки
                'Y',                                         // активен
                ConvertTimeStamp(time() + 60, 'FULL')        // первый запуск через минуту
            );
        }
    }

    public function InstallDB(): bool
    {
       global $DB, $APPLICATION;

        $connection = \Bitrix\Main\Application::getConnection();
		$installSqlFile = __DIR__. '/db/' . \Bitrix\Main\Application::getConnection()->getType() . '/install.sql';
		if (!$connection->isTableExists('garage_cars') && is_file($installSqlFile)) {
	
			$errors = $DB->RunSQLBatch($installSqlFile);
			if ($errors !== false)
			{
				$APPLICATION->ThrowException(implode('<br>', $errors));
				return false;
			}

		}

  
        return true;
    }

    // зарегистрировать зависимости на события модулей
    public  function InstallEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->registerEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            'lukasheva.garage',
            'Lukasheva\Garage\OnEntityDetailsTabsInitializedHandler',
            'setGarageTab');

        $eventManager->registerEventHandler(
            'crm',
            'OnBeforeCrmDealAdd',
            'lukasheva.garage',
            'Lukasheva\Garage\GarageDealHandler',
            'OnBeforeCrmDealAdd');
    }

    // удалить все зависимости на событиях
    public  function UnInstallEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            'lukasheva.garage',
            'Lukasheva\Garage\OnEntityDetailsTabsInitializedHandler',
            'setGarageTab'
        );

        $eventManager->unRegisterEventHandler(
            'crm',
            'OnBeforeCrmDealAdd',
            'lukasheva.garage',
            'Lukasheva\Garage\GarageDealHandler',
            'OnBeforeCrmDealAdd'
        );
    }

    public function UnInstallDB(array $arParams = []): bool
	{
		global $DB, $APPLICATION;

		if (
			(!array_key_exists('save_tables', $arParams) || $arParams['save_tables'] !== 'Y')
			&& is_file(__DIR__ . '/db/' . \Bitrix\Main\Application::getConnection()->getType() . '/uninstall.sql')
		)
		{
			$errors = $DB->RunSQLBatch(__DIR__ . '/db/' . \Bitrix\Main\Application::getConnection()->getType() . '/uninstall.sql');
			if ($errors !== false)
			{
				$APPLICATION->ThrowException(implode('<br>', $errors));

				return false;
			}
		}

        Option::delete($this->MODULE_ID);

		ModuleManager::unRegisterModule($this->MODULE_ID);

		return true;
	}

    public function InstallFiles()
    {
        CopyDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/local/components', true, true);
    }

    public function UnInstallFiles()
    {
        DeleteDirFilesEx('/local/components/lukasheva');
        return true;
    }
}
?>