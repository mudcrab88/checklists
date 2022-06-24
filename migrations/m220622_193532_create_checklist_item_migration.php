<?php

use yii\db\Migration;

/**
 * Class m220622_193532_create_checklist_item_migration
 */
class m220622_193532_create_checklist_item_migration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable( 'checklist_item', [
            'id' => $this->primaryKey()->comment( 'Идентификатор' ),
            'name' => $this->string()->notNull()->comment( 'Название' ),
            'checklist_id' => $this->integer()->comment('Пользователь'),
            'checked' => $this->boolean()->defaultValue( false )->comment( 'Выполнен' )
        ], 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB');

        $this->addForeignKey('fk-checklist_id-checklist_item', 'checklist_item', 'checklist_id', 'checklist', 'id');

        $this->createIndex( 'idx-name-checklist_id', 'checklist_item', [ 'name', 'checklist_id' ], true );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-name-checklist_id', 'checklist_item');

        $this->dropForeignKey('fk-checklist_id-checklist_item', 'checklist_item');

        $this->dropTable('checklist_item');
    }
}
