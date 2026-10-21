<?php
namespace Otus\Workday;

use Bitrix\Main\UI\Extension;
use Bitrix\Main\Context;

class EventHandler 
{
    public static function onBeforeProlog()
    {
        // не подключаем в админке и в ajax-запросах
        $request = Context::getCurrent()->getRequest();
        if ($request->isAjaxRequest() || defined('ADMIN_SECTION')) {
            return;
        }

        Extension::load('otus.workday');
        
    }
}