<?php

namespace Lukasheva\Garage\Orm;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\EnumField;
	
Loc::loadMessages(__FILE__);

/**
 * Class ZajavkyTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> DATE_CREATE datetime optional
 * <li> CREATED_BY_ID int optional
 * <li> STATUS_ZAJAVKY string(20) optional
 * <li> TYPE_ZAJAVKY string(20) optional
 * </ul>
 *
 * @package Lukasheva\Garage
 **/

class ZajavkyTable extends DataManager
{

	public const TYPE_AUTO = 'AUTO';
    public const TYPE_MANUAL = 'MANUAL';

    public const STATUS_NEW = 'NEW'; //Создана 
    public const STATUS_DONE = 'DONE'; // Выполнена
    public const STATUS_REJECTED = 'REJECTED'; // Отклонена
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'garage_zajavky';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			'ID' => (new IntegerField('ID',
				))->configureTitle(Loc::getMessage('ZAJAVKY_ENTITY_ID_FIELD'))
						->configurePrimary(true)
						->configureAutocomplete(true),

			'DATE_CREATE' => (new DatetimeField('DATE_CREATE',
				[
					'default_value' => function () {
						return new \Bitrix\Main\Type\DateTime();
					},	
				]
			))->configureTitle(Loc::getMessage('ZAJAVKY_ENTITY_DATE_CREATE_FIELD')),

			'CREATED_BY_ID' => (new IntegerField('CREATED_BY_ID',
				[
					//'validation' => [__CLASS__, 'validateCreatedId']
					'required' => true,
					'default_value' => null,
				]
			))->configureTitle(Loc::getMessage('ZAJAVKY_ENTITY_CREATED_BY_ID_FIELD')),

			'DEAL_ID' => (new IntegerField('DEAL_ID',
				[
					//'validation' => [__CLASS__, 'validateCreatedId']
					'required' => true,
					'default_value' => null,
				]
			))->configureTitle(Loc::getMessage('ZAJAVKY_ENTITY_DEAL_ID_FIELD')),

			'STATUS_ZAJAVKY' => (new EnumField('STATUS_ZAJAVKY',
				[
					'values' => [self::STATUS_NEW, self::STATUS_DONE, self::STATUS_REJECTED],
					//'validation' => [__CLASS__, 'validateStatus'],
					'default_value' => self::STATUS_NEW
				]
			))->configureTitle(Loc::getMessage('ZAJAVKY_ENTITY_STATUS_ZAJAVKY_FIELD')),

			'TYPE_ZAJAVKY' => (new EnumField('TYPE_ZAJAVKY',
				[
					'values' => [self::TYPE_AUTO, self::TYPE_MANUAL],
					//'validation' => [__CLASS__, 'validateType'],
					'default_value' => self::TYPE_MANUAL,
				]
			))->configureTitle(Loc::getMessage('ZAJAVKY_ENTITY_TYPE_ZAJAVKY_FIELD')),

		];
	}

	
}