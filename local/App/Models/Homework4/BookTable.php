<?php
/**
 * Справочная информация 
 * https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&CHAPTER_ID=05748&LESSON_PATH=3913.3516.5748
 * https://academy.1c-bitrix.ru/courses/?COURSE_ID=370&LESSON_ID=28622&LESSON_PATH=28608.28622 
 * Свои сущности на базе Bitrix Framework 
 */
namespace App\Models\Homework4;


use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\EventResult;
use Bitrix\Main\ORM\Fields\Validators\RegExpValidator;
use Bitrix\Main\Type\Date;

class BookTable extends DataManager
{
	/**
	 * Returns table name
	 * @return string
	 */
	public static function getTableName()
	{
		return 'my_book';
	}

	/**
	 * Returns table structure
	 *
	 * @return array
	 */
	public static function getMap()
    {
        return [
            (new Fields\IntegerField('ID'))
                // ID — первичный ключ с автоинкрементом.
                ->configurePrimary()
                ->configureAutocomplete(),

            (new Fields\StringField('ISBN'))
                // В таблице поле хранится в столбце ISBNCODE.
                ->configureRequired()
                ->configureColumnName('ISBNCODE')
                ->addValidator(new RegExpValidator('/^\d{13}$/')),

            (new Fields\StringField('TITLE'))
                ->configureRequired(),

            (new Fields\DateField('PUBLISH_DATE'))
                ->configureDefaultValue(static function (): Date
                {
                    return new Date(date('Y-m-d'), 'Y-m-d');
                }),

            (new Fields\ArrayField('EDITIONS_ISBN'))
                // Массив ISBN хранится в JSON.
                ->configureSerializationJson(),

            (new Fields\IntegerField('READERS_COUNT'))
                ->configureDefaultValue(0),
			new Fields\IntegerField('PUBLISHER_ID', [
                'required' => true,
            ]), // Поле PUBLISHER_ID, целочисленное
			 // Определение связи с таблицей PublisherTable
			 new Reference(
                'PUBLISHER', // Имя связи
                MypublisherTable::class, // Класс связанной таблицы
                Join::on('this.PUBLISHER_ID', 'ref.IBLOCK_ELEMENT_ID') // Условие соединения: связывает PUBLISHER_ID с ID в PublisherTable
            ), //->configureJoinType('inner'), // Установка типа соединения: INNER JOIN */
        ];
    }

	public static function onBeforeAdd(Event $event): EventResult
    {
		pr('onBeforeAdd');
        $result = new EventResult();
        $fields = $event->getParameter('fields');

        if (isset($fields['ISBN']))
        {
            $result->modifyFields([
                'ISBN' => str_replace('-', '', $fields['ISBN']),
            ]);
        }

        return $result;
    }
}
