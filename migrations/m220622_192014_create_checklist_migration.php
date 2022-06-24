<?php

use yii\db\Migration;

/**
 * Class m220622_192014_create_checklist_migration
 */
class m220622_192014_create_checklist_migration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable( 'checklist', [
            'id' => $this->primaryKey()->comment( 'Идентификатор' ),
            'name' => $this->string()->notNull()->comment( 'Название' ),
            'user_id' => $this->integer()->comment('Пользователь')
        ], 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB');

        $this->addForeignKey('fk-user_id_checklist', 'checklist', 'user_id', 'user', 'id');

        $this->createIndex( 'idx-name-user_id', 'checklist', [ 'name', 'user_id' ], true );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-name-user_id', 'checklist');

        $this->dropForeignKey('fk-user_id_checklist', 'checklist');

        $this->dropTable('checklist');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220622_192014_create_checklist_migration cannot be reverted.\n";

        return false;
    }
    */
}
