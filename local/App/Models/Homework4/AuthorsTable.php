<?php

namespace App\Models\Homework4;

use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Data\DataManager;
use CIBlockElement;
//use App\Models\AbstractIblockPropertyValuesTable;

class AuthorsTable extends DataManager
{
    public const IBLOCK_ID = 18;
    protected static ?array $properties = null;

     /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'b_iblock_element_prop_s'.static::IBLOCK_ID;
    }

    public static function getMap(): array
    {
        $map['IBLOCK_ELEMENT_ID'] = new IntegerField('IBLOCK_ELEMENT_ID', ['primary' => true]); //определяем примари-кей
        
        /**
         * Добавлем свойства в список полей, под читаемыми именами,
         * т.к. в таблице 'b_iblock_element_prop_s'.static::IBLOCK_ID  они называются PROPERTY_77 и PROPERTY_78 
         */
       /*  $map['FIO_AUTHOR'] = new StringField("PROPERTY_77");
        $map['YEAR_BORN'] = new IntegerField('PROPERTY_78'); */

        /**
         * Добавлем свойства в список полей, под читаемыми именами,
         * т.к. в таблице 'b_iblock_element_prop_s'.static::IBLOCK_ID  они называются PROPERTY_77 и PROPERTY_78 
         * в цикле, без привязки к имени
         */
        foreach (static::getProperties() as $property) {
            if ($property['MULTIPLE'] === 'Y') {
                continue; //множественные поля в данной модели не используются
            }
            if ($property['PROPERTY_TYPE'] == PropertyTable::TYPE_NUMBER) {
                $map[$property['CODE']] = new IntegerField("PROPERTY_{$property['ID']}");
            } elseif ($property['USER_TYPE'] === 'Date') {
                $map[$property['CODE']] = new DatetimeField("PROPERTY_{$property['ID']}");
            } else {
                $map[$property['CODE']] = new StringField("PROPERTY_{$property['ID']}");
            }
        }
        return $map;
    }

    public static function getProperties(): array
    {
        if (isset(static::$properties[static::IBLOCK_ID])) {
            return static::$properties[static::IBLOCK_ID];
        }

        $dbResult = PropertyTable::query()
            ->setSelect(['ID', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE', 'NAME', 'USER_TYPE'])
            ->where('IBLOCK_ID', static::IBLOCK_ID)
            ->exec();
        while ($row = $dbResult->fetch()) {
            static::$properties[static::IBLOCK_ID][$row['CODE']] = $row;
        }

        return static::$properties[static::IBLOCK_ID] ?? [];
    }
    
}
