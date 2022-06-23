<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class Checklist
 *
 * @property int    $id
 * @property string $name
 * @property int    $user_id
 */

class Checklist extends ActiveRecord
{
    public function attributeLabels(): array
    {
        return [
            'id'      => 'ID',
            'name'    => 'Название',
            'user_id' => 'ID пользователя'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'checklist';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['name', 'user_id'], 'required'],
            [['user_id'], 'integer'],
        ];
    }
}
