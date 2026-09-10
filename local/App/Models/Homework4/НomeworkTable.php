<?php
namespace App\Models\Homework4;


use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;

class НomeworkTable extends DataManager
{
	/**
	 * Returns table name
	 * @return string
	 */
	public static function getTableName()
	{
		return 'homework_four';
	}

	/**
	 * Returns table structure
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			// числовой тип данных
			new Fields\IntegerField(
                'ID', 
                [
				'primary' => true,
				'autocomplete' => true,
			    ]
            ),
			// строковый тип данных
			new Fields\StringField(
                'DATA', 
                [
				    'required' => true,
			    ]
            ), 
            new Fields\BooleanField(
                'ACTIVE',
                [
                    'values' => array('N','Y'),
                    'default_values' => 'N'
                ]
            ),
			 // Связываемый тип данных. Определение связи с таблицей MypublisherTable
			new Fields\IntegerField('PUBLISHER_ID', [
                'required' => true,
            ]), 
			
			new Reference(
                'PUBLISHER', // Имя связи
                MypublisherTable::class, // Класс связанной таблицы
                Join::on('this.PUBLISHER_ID', 'ref.IBLOCK_ELEMENT_ID') // Условие соединения: связывает PUBLISHER_ID с ID в PublisherTable
            ), //->configureJoinType('inner'), // Установка типа соединения: INNER JOIN

			// Связываемый тип данных. Определение связи с таблицей AuthorTable
			new Fields\IntegerField('AUTHOR_ID', [
                'required' => true,
            ]), 
			
			new Reference(
                'AUTHOR', // Имя связи
                AuthorsTable::class, // Класс связанной таблицы
                Join::on('this.AUTHOR_ID', 'ref.IBLOCK_ELEMENT_ID') // Условие соединения: связывает PUBLISHER_ID с ID в AuthorsTable
            ), //->configureJoinType('inner'), // Установка типа соединения: INNER JOIN
		];
	}
}
