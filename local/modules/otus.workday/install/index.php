<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class otus_workday extends CModule
{
    public $MODULE_ID = 'otus.workday';
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

        $this->MODULE_NAME = Loc::getMessage('WD_INSTALL_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('WD_MODULE_DESCRIPTION');
        
    }

    public function DoUninstall(): void
	{
		global $USER;

		if (!$USER->IsAdmin())
		{
			return;
		}

		$this->UnInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);
        $this->UnInstallEvents();

	}
    
    public function DoInstall()
    {
        global $USER;

         if (!check_bitrix_sessid())
        {
            return false;
        }

        if (!$USER->IsAdmin())
        {
            return;
        }

        ModuleManager::registerModule($this->MODULE_ID);

        $this->InstallFiles();
        $this->InstallEvents();

    }

    public function InstallFiles()
    {
        CopyDirFiles(__DIR__ . '/js', $_SERVER['DOCUMENT_ROOT'] . '/local/js', true, true);
    }

    public function UnInstallFiles()
    {
        DeleteDirFilesEx('/local/js/workday');
        return true;
    }

    // зарегистрировать зависимости на события модулей
    public  function InstallEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->registerEventHandler(
            'main',
            'OnBeforeProlog',
            $this->MODULE_ID,
            'Otus\Workday\EventHandler',
            'onBeforeProlog');
    }

    // удалить все зависимости на событиях
    public  function UnInstallEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            'main',
            'OnBeforeProlog',
            $this->MODULE_ID,
            'Otus\Workday\EventHandler',
            'onBeforeProlog');
    }
}
?>