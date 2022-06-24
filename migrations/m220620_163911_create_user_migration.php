<?php

use yii\db\Migration;

/**
 * Class m220620_163911_create_user_migration
 */
class m220620_163911_create_user_migration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable( 'user', [
            'id' => $this->primaryKey()->comment( 'Идентификатор' ),
            'username' => $this->string()->notNull()->unique()->comment( 'Логин' ),
            'email' => $this->string()->notNull()->unique()->comment( 'E-mail' ),
            'password' => $this->string()->notNull()->comment( 'Пароль(хэш)' ),
            'auth_key' => $this->string()->notNull()->comment('Ключ аутентификации'),
            'status' => $this->string()->notNull()->defaultValue('new')->comment('Ключ аутентификации'),
            'checklists_max' => $this->integer()->notNull()->defaultValue(10)->comment( 'Количество чек-листов' ),
            'access_token' => $this->string()->notNull()->comment( ' Токен' ),
        ], 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB');

        $hash = \Yii::$app->getSecurity()->generatePasswordHash('admin');

        $this->insert('user', [
            'username' => 'admin',
            'email' => 'admin@admin.local',
            'password' => $hash,
            'auth_key' => \Yii::$app->getSecurity()->generateRandomString(32),
            'checklists_max' => 10,
            'status' => 'active',
            'access_token' => base64_encode('admin:'.$hash)
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('user');
    }
}
