<?php

namespace Lukasheva\Garage\Orm;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;
use \Bitrix\Main\Entity\ReferenceField;


/**
 * Class ModelsTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> MAKE_ID int optional
 * <li> MODEL string(50) optional
 * </ul>
 *
 **/

class ModelsTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'garage_cars_models';
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
					'title' => Loc::getMessage('MODELS_ENTITY_ID_FIELD'),
				]
			),
			new IntegerField(
				'MAKE_ID',
				[
					'title' => Loc::getMessage('MODELS_ENTITY_MAKE_ID_FIELD'),
				]
			),
			'MAKES' => new ReferenceField(  
                'MAKES', 
                MakesTable::class, 
                ['=this.MAKE_ID' => 'ref.ID']
            ),
			new StringField(
				'MODEL',
				[
					'validation' => function()
					{
						return[
							new LengthValidator(null, 50),
						];
					},
					'title' => Loc::getMessage('MODELS_ENTITY_MODEL_FIELD'),
				]
			),
		];
	}
}