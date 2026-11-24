<?php

namespace Lukasheva\Garage\Orm;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use \Bitrix\Main\Entity\ReferenceField;

Loc::loadMessages(__FILE__);

/**
 * Class DetailTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> ZAJAVKA_ID int optional
 * <li> PRODUCT_ID int optional
 * <li> QUANTITY int optional
 * </ul>
 *
 * @package Lukasheva\Garage
 **/

class DetailTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'garage_zajavky_detail';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			new IntegerField(
				'ID',
				[
					'primary' => true,
					'autocomplete' => true,
					'title' => Loc::getMessage('DETAIL_ENTITY_ID_FIELD'),
				]
			),
			new IntegerField(
				'ZAJAVKA_ID',
				[
					'title' => Loc::getMessage('DETAIL_ENTITY_ID_ZAJAVKA_FIELD'),
                    'required' => true,
				]
			),
			new IntegerField(
				'PRODUCT_ID',
				[
					'title' => Loc::getMessage('DETAIL_ENTITY_PRODUCT_ID_FIELD'),
                    'required' => true,
				]
			),
			new IntegerField(
				'QUANTITY',
				[
					'title' => Loc::getMessage('DETAIL_ENTITY_QUANTITY_FIELD'),
                    'validation' => [__CLASS__, 'validateQuantity'],
                    'required' => true,
                    'default_value' => 1,
				]
			),
            'ZAJAVKY' => new ReferenceField(  
                'ZAJAVKY', 
                ZajavkyTable::class, 
                ['=this.ZAJAVKA_ID' => 'ref.ID']
            ),
		];
	}

    /**
	 * Returns validators for QUANTITY field.
	 *
	 * @return array
	 */
	public static function validateQuantity(): array
	{
		return [
			function ($value) {
				$quantity = (int)$value;
				if ($quantity < 0) {
					return Loc::getMessage('DETAIL_ENTITY_QUANTITY_FIELD_ERROR');
				}
				return true; 
			},
		];
	}
}