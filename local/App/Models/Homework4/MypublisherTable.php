<?php

namespace App\Models\Homework4;

use \Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use App\Models\AbstractIblockPropertyValuesTable; 
use Bitrix\Main\ORM\Fields\IntegerField;

/**
 * Для создания модели использую абстрактный класс из предыдущего ДЗ
 */
class MypublisherTable extends AbstractIblockPropertyValuesTable
{
    public const IBLOCK_ID = 25;

}
