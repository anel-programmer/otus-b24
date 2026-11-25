<?php
namespace Lukasheva\Garage\Orm;

use Bitrix\Main\ORM\Data\DataManager,
	Bitrix\Main\ORM\Fields\IntegerField,
	Bitrix\Main\ORM\Fields\StringField,
	Bitrix\Main\ORM\Fields\Validators\LengthValidator,
	Bitrix\Main\Localization\Loc;
	
Loc::loadMessages(__FILE__);

/**
 * Class CarsTable
 *
 * @package Models
*/

class CarsTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'garage_cars';
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
				))->configureTitle(Loc::getMessage('LISTS_ENTITY_ID_FIELD'))
						->configurePrimary(true)
						->configureAutocomplete(true),
			'CONTACT_ID' => (new IntegerField('CONTACT_ID',
						[])),
			'CARMAKE' => (new StringField('CARMAKE',
					[
						'validation' => [__CLASS__, 'validateCarmake']
					]
				))->configureTitle(Loc::getMessage('FIELD_CARMAKE')),
			'MODEL' => (new StringField('MODEL',
					[
						'validation' => [__CLASS__, 'validateModel']
					]
				))->configureTitle(Loc::getMessage('FIELD_MODEL')),
			'LICENSE_PLATE_NUMBER' => (new StringField('LICENSE_PLATE_NUMBER',
					[
						'validation' => [__CLASS__, 'validateLicensePlateNumber']
					]
				))->configureTitle(Loc::getMessage('FIELD_LICENSE_PLATE_NUMBER')),
			'YEAR_OF_MANUFACTURE' => (new StringField('YEAR_OF_MANUFACTURE',
					[
						'validation' => [__CLASS__, 'validateYearOfManufacture']
					]
				))->configureTitle(Loc::getMessage('FIELD_YEAR_OF_MANUFACTURE')),
			'CAR_COLOR' => (new StringField('CAR_COLOR',
					[
						'validation' => [__CLASS__, 'validateCarColor']
					]
				))->configureTitle(Loc::getMessage('FIELD_CAR_COLOR')),
			'MILEAGE' => (new StringField('MILEAGE',
					[
						'validation' => [__CLASS__, 'validateMileage']
					]
				))->configureTitle(Loc::getMessage('FIELD_MILEAGE')),
		];
	}

	/**
	 * Returns validators for CARMAKE field.
	 *
	 * @return array
	 */
	public static function validateCarmake()
	{
		return [
			function ($value) {
				$carmake = trim((string)$value);
				if (mb_strlen($carmake) > 51 || mb_strlen($carmake) < 1) {
					return Loc::getMessage('FIELD_CARMAKE_ERROR');
				}
				return true;
			},
		];
	}

	/**
	 * Returns validators for MODEL field.
	 *
	 * @return array
	 */
	public static function validateModel()
	{
		return [
			function ($value) {
				$model = trim((string)$value);
				if (mb_strlen($model) > 51 || mb_strlen($model) < 1) {
					return Loc::getMessage('FIELD_MODEL_ERROR');
				}
				return true;
			},
		];
	}

	/**
	 * Returns validators for LICENSE_PLATE_NUMBER field.
	 *
	 * @return array
	 */
	public static function validateLicensePlateNumber()
	{
		return [
			function ($value) {
				$pattern = '/^[А-Я]\d{3}[А-Я]{2}\d{2,3}$/iu';
				$plateNumber = (string)$value;
				if (!preg_match($pattern, $plateNumber)) {
					return Loc::getMessage('FIELD_LICENSE_PLATE_NUMBER_ERROR');
				}
				return true;
			}
		];
	}

	/**
	 * Returns validators for YEAR_OF_MANUFACTURE field.
	 *
	 * @return array
	 */
	public static function validateYearOfManufacture(): array
	{
		return [
			function ($value) {
				$year = (int)$value;
				//ctype_digit - Проверяет, состоит ли переданная строка (string) text только из цифровых символов
				if ($year < 1900 || $year > (int)date('Y') || !ctype_digit((string)$value)) {
					return Loc::getMessage('FIELD_YEAR_OF_MANUFACTURE_ERROR');
				}
				return true; 
			},
		];
	}

	/**
	 * Returns validators for CAR_COLOR field.
	 *
	 * @return array
	 */
	public static function validateCarColor()
	{
		return [
			function ($value) {
				$color = trim((string)$value);
				if (mb_strlen($color) > 51) {
					return Loc::getMessage('FIELD_CAR_COLOR_ERROR');
				}
				return true;
			},
		];
	}

	/**
	 * Returns validators for MILEAGE field.
	 *
	 * @return array
	 */
	public static function validateMileage()
	{
		return [
			function ($value) {
				$mileage = trim((string)$value);

				if ($mileage === '') {
					return true;
				}

				if (!ctype_digit($mileage)) {
					return Loc::getMessage('FIELD_MILEAGE_ERROR');
				}

				return true;
			},
		];
	}
}