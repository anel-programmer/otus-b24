<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class myotus_custommodule extends CModule
{
    public $MODULE_ID = 'myotus.custommodule';
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

        $this->MODULE_NAME = Loc::getMessage('OTUS_INSTALL_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('OTUS_MODULE_DESCRIPTION');
        
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
				Loc::getMessage('OTUS_UNINSTALL_TITLE'),
				__DIR__. '/unstep1.php',
			);
		}
		elseif ($step === 2)
		{
			$this->UnInstallDB([
				'save_tables' => $_REQUEST['save_tables'] ?? 'N',
			]);
			$this->UnInstallFiles();

			$APPLICATION->IncludeAdminFile(
				Loc::getMessage('OTUS_UNINSTALL_TITLE'),
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

        $this->InstallDB();
        $this->InstallFiles();

        /*$APPLICATION->IncludeAdminFile(
            'Установка модуля ' . $this->MODULE_NAME,
            __DIR__ . '/step.php'
        );*/
    }

    public function InstallDB(): bool
    {
       global $DB, $APPLICATION;

		$freshSchema = false;
		$installSqlFile = __DIR__. '/db/' . \Bitrix\Main\Application::getConnection()->getType() . '/install.sql';
		if (is_file($installSqlFile))
		{
			$errors = $DB->RunSQLBatch($installSqlFile);
			if ($errors !== false)
			{
				$APPLICATION->ThrowException(implode('<br>', $errors));

				return false;
			}

			$freshSchema = true;
		}

        $this->InstallEvents();
        return true;
    }

    // зарегистрировать зависимости на события модулей
    public  function InstallEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->registerEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            'myotus.custommodule',
            'Myotus\Custommodule\OnEntityDetailsTabsInitializedHandler',
            'setCustomTabs');
    }

    // удалить все зависимости на событиях
    public  function UnInstallEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            'myotus.custommodule',
            'Myotus\Custommodule\OnEntityDetailsTabsInitializedHandler',
            'setCustomTabs'
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

		 $this->UnInstallEvents();

		ModuleManager::unRegisterModule($this->MODULE_ID);

		return true;
	}

    public function InstallFiles()
    {
        CopyDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/local/components', true, true);
    }

    public function UnInstallFiles()
    {
        DeleteDirFilesEx('/local/components/myotus');
        return true;
    }
}
?>