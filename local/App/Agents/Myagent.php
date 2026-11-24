<?php

namespace App\Agents;

use Bitrix\Crm\Service\Container;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;

class Myagent
{
    public static function cleanOldDeals(int $days): string
    {
        Loader::includeModule('crm');
        $dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);
        $deals = $dealFactory->getItems([
            'filter' => [
                '<DATE_CREATE' => ((new DateTime)->add(sprintf('-%d days', $days)))->format('d.m.Y H:I:s'),
                '=STAGE_ID' => 'NEW',
            ],
            'select' => ['ID'],
        ]);

        foreach ($deals as $deal) {
            $deleteOperation = $dealFactory->getDeleteOperation($deal);
            $deleteOperation->disableAllChecks();
            $deleteOperation->launch();
        }

        return sprintf('\App\Agents\Myagent::cleanOldDeals(%d);', $days);
    }
}