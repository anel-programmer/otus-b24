<?php

namespace Lukasheva\Garage\Orm;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;




/**
 * Class MakesTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> MAKE string(50) optional
 * </ul>
 *
 **/

class MakesTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'garage_cars_makes';
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
					'title' => Loc::getMessage('MAKES_ENTITY_ID_FIELD'),
				]
			),
			new StringField(
				'MAKE',
				[
					'validation' => function()
					{
						return[
							new LengthValidator(null, 50),
						];
					},
					'title' => Loc::getMessage('MAKES_ENTITY_MAKE_FIELD'),
				]
			),
			(new OneToMany('MODELS', ModelsTable::class, 'MAKE')),
		];
	}
}