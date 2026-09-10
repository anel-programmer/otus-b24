<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
/** @global $APPLICATION */
$APPLICATION->SetTitle('Пример создания кастомной таблицы Создание своих таблиц БД и написание модели данных к ним//ДЗ4');

use Bitrix\Main\Application;
use Bitrix\Main\ORM;
use Bitrix\Main\Type\Date;
use Bitrix\Main\ORM\Entity;
use App\Models\Homework4\BookTable as BookTable;
use Bitrix\Tasks\Flow\Notification\Config\Where;

/**
 * Проверяем существует таблица или нет
 * Если таблица существует, то удаляем.
 */
 if (Application::getConnection(BookTable::getConnectionName())
    ->isTableExists(ORM\Entity::getInstance(BookTable::class)->getDBTableName())
) {
    Application::getConnection(BookTable::getConnectionName())
        ->queryExecute('drop table if exists ' . ORM\Entity::getInstance(BookTable::class)
                ->getDBTableName());
    pr('Таблица '.BookTable::getTableName().' успешно удалена!');
} 

/**
 * Проверяем существует таблица или нет
 * Если таблица не найдена, то создаем.
 */
if (!Application::getConnection(BookTable::getConnectionName())->isTableExists(
            ORM\Entity::getInstance(BookTable::class)->getDBTableName()
        )
) {
    ORM\Entity::getInstance(BookTable::class)->createDbTable();
    pr('Таблица '.BookTable::getTableName().' успешно СОЗДАНА!');
}


/**
 * Добавление записи
 */
if (Application::getConnection(BookTable::getConnectionName())->isTableExists(
            ORM\Entity::getInstance(BookTable::class)->getDBTableName()
        )
) {
    $result = BookTable::add( [
            'ISBN' => '978-0134757599',
            'TITLE' => 'Refactoring',
            'PUBLISH_DATE' => new \Bitrix\Main\Type\DateTime('16.11.2002 10:30:00'),
            'PUBLISHER_ID' => '61',
        ]);

    if ($result->isSuccess())
    {
        // ID понадобится для последующего update, delete или выборки записи.
        $id = $result->getId();
        pr('Запись с id = '.$id.' успешно добавлена!');
    } 

    // Метод addMulti() добавляет несколько записей одним вызовом. Он принимает массив записей и возвращает такой же объект результата.
    // Замечание! - При множественном добавлении событие onBeforeAdd не срабатывает
    $result = BookTable::addMulti([
        [
            'ISBN' => '9780321127426',
            'TITLE' => 'Patterns of Enterprise Application Architecture',
            'PUBLISH_DATE' => new \Bitrix\Main\Type\Date('16.11.2002'),
            'PUBLISHER_ID' => '60',
        ],
        [
            'ISBN' => '9780134757599',
            'TITLE' => 'Refactoring',
            'PUBLISH_DATE' => new \Bitrix\Main\Type\DateTime('16.11.2002 10:30:00'),
            'PUBLISHER_ID' => '60',
        ],
    ], true);

    if (!$result->isSuccess())
    {
        dump($result->getErrorMessages());
        pr('Записи не были добавлены, т.к. значение ISBN не было преобразовано к нужному формату. Событие onBeforeAdd не отработало!');
    }

    /**
     * Обновить запись
     */
   /*  $result = BookTable::update($id, [
        'PUBLISH_DATE' => new Date('2002-11-15', 'Y-m-d'),
    ]);

    if ($result->isSuccess())
    {
        pr('Было изменено '. $result->getAffectedRowsCount().' строк использую метод update');
    }
    else
    {
        var_dump($result->getErrorMessages());
    } */

    /**
     * updateMulti() обновляет несколько записей одним набором значений. Он принимает массив первичных ключей и массив новых значений.
     */
    $firstBookId = 1;
    $secondBookId = 2;
    $result = BookTable::updateMulti(
        [$firstBookId, $secondBookId],
        ['PUBLISHER_ID' => 60],
        true
    );

    if ($result->isSuccess())
    {
       pr('Было изменено '. $result->getAffectedRowsCount().' строк использую метод updateMulti');
    }
    else
    {
        var_dump($result->getErrorMessages());
    }

    /**
     * Удалить запись
     */
    // Метод delete удаляет запись по первичному ключу
   /*  $result = BookTable::delete($id);
    if ($result->isSuccess())
    {
        pr('Была удалена запись с id  '. $id.' использую метод delete');
    }
    else
    {
        var_dump($result->getErrorMessages());
    } */


    /**
     * Вывод записей методом getList
     */
    $result = BookTable::getList(array('select' => array('*')));
    $rows = $result->fetchAll();
    dump($rows);

    /**
     * аналогично через Entity\Query
    */ 
    $query = BookTable::query()
        ->addSelect('TITLE')
        ->addSelect('PUBLISHER_ID')
        ->setLimit(5)
    ;
    $books = $query->fetchCollection();
    dump($books);

    $query = BookTable::query()
                ->addSelect('*')
                ->where("ID",1)
                ->exec()->fetchAll();
    dump($query);

}


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>