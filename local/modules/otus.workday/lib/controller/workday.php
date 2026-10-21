<?php
namespace Otus\Workday\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Loader;
use Bitrix\Main\Engine\CurrentUser;

class Workday extends Controller
{
    public function startAction()
    {
        Loader::includeModule('timeman');

        $tmUser = new \CTimeManUser(CurrentUser::get()->getId());
        $state = $tmUser->State(); // OPENED | CLOSED | PAUSED | EXPIRED

        if ($state === 'CLOSED') {
            $result = $tmUser->OpenDay();
            if (!$result){
                 $tmUser->ReopenDay(); // возобновление после завершения
            }

        } elseif ($state === 'PAUSED') {
            $tmUser->ReopenDay(); // возобновление после паузы
        } elseif ($state === 'EXPIRED') {
            $this->addError(new \Bitrix\Main\Error('День просрочен, сначала нужно закрыть его с отчётом'));
            return null;
        }

        return ['state' => $tmUser->State()];
    }
}