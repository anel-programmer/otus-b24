<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
/** @global $APPLICATION */
$APPLICATION->SetTitle('Создание своих таблиц БД и написание модели данных к ним//ДЗ4');

use Bitrix\Main\Application;
use Bitrix\Main\ORM;
use App\Models\Homework4\НomeworkTable as HWTable;

/**
 * Проверяем существует таблица или нет
 * Если таблица существует, то удаляем.
 */
  /* if (Application::getConnection(HWTable::getConnectionName())
    ->isTableExists(ORM\Entity::getInstance(HWTable::class)->getDBTableName())
) {
    Application::getConnection(HWTable::getConnectionName())
        ->queryExecute('drop table if exists ' . ORM\Entity::getInstance(HWTable::class)
                ->getDBTableName());
    pr('Таблица '.HWTable::getTableName().' успешно удалена!');
}   */
/**
 * Проверяем существует таблица или нет
 * Если таблица не найдена, то создаем.
 */
if (!Application::getConnection(HWTable::getConnectionName())->isTableExists(
            ORM\Entity::getInstance(HWTable::class)->getDBTableName()
        )
) {
    ORM\Entity::getInstance(HWTable::class)->createDbTable();
    pr('Таблица '.HWTable::getTableName().' успешно СОЗДАНА!');
}

/**
 * Добавление записей если таблица существует и пустая
 */
if (Application::getConnection(HWTable::getConnectionName())->isTableExists(
            ORM\Entity::getInstance(HWTable::class)->getDBTableName() && (HWTable::getCount()==0)
        )
) {


    $lastId = HWTable::getCount();
    $result = HWTable::add( [
            'DATA' => 'Книга '.$lastId,
            'ACTIVE' => 'Y',
            'PUBLISHER_ID' => '37',
            'AUTHOR_ID' => '35',
        ]);

    if ($result->isSuccess())
    {
        // ID понадобится для последующего update, delete или выборки записи.
        $id = $result->getId();
        pr('Запись с id = '.$id.' успешно добавлена!');
    } 

    $lastId = HWTable::getCount();
    $result = HWTable::add( [
            'DATA' => 'Тест '.$lastId,
            'ACTIVE' => 'Y',
            'PUBLISHER_ID' => '36',
            'AUTHOR_ID' => '34',
        ]);

    if ($result->isSuccess())
    {
        // ID понадобится для последующего update, delete или выборки записи.
        $id = $result->getId();
        pr('Запись с id = '.$id.' успешно добавлена!');
    } 
}

if (Application::getConnection(HWTable::getConnectionName())->isTableExists(
            ORM\Entity::getInstance(HWTable::class)->getDBTableName() 
        )) 
{
    /**
     * Вывод записей методом getList
     */
    $result = HWTable::getList(array('select' => array('*','PUBLISHER','AUTHOR')));
    $rows = $result->fetchAll();
    //dump($rows);
    ?><ul><?
    foreach ($rows as $row){
        ?>
        <li>
            <?= $row['DATA'] ?> - <?= $row['APP_MODELS_HOMEWORK4_нOMEWORK_PUBLISHER_PBNAME'] ?> -  <?= $row['APP_MODELS_HOMEWORK4_нOMEWORK_AUTHOR_FIO_AUTHOR'] ?>
        </li>
        <?
    }
    ?></ul><?
    /**
     * аналогично через Entity\Query
    */ 
   /* $query = HWTable::query()
        ->addSelect('DATA')
        ->addSelect('PUBLISHER_ID')
        ->addSelect('PUBLISHER')
        ->addSelect('AUTHOR')
      ;
    $books = $query->fetchAll();
    dump($books); */

   /*  $query = HWTable::query()
                ->addSelect('*')
                ->exec()
                ->fetchObject();
    dump($query);  */

    pr('*************************');
    $books = HWTable::getByPrimary(1, [
        'select' => ['*', 'PUBLISHER', 'AUTHOR'], // Выбор всех полей книги и связанных данных издательства и автора записи с ID=1
    ])->fetchObject();
    pr('Выбор всех полей книги и связанных данных издательства и автора записи с ID=1');
    pr($books->getData().' -  '.$books->getPublisher()->getPbname().' - '.$books->getAuthor()->getFio_author());
    pr('*************************');
     
}



require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>