<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class ChecklistItem
 *
 * @property int     $id
 * @property string  $name
 * @property int     $checklist_id
 * @property boolean $checked
 */

class ChecklistItem extends ActiveRecord
{
    public function attributeLabels(): array
    {
        return [
            'id'           => 'ID',
            'name'         => 'Название',
            'checklist_id' => 'ID чек-листа',
            'checked'      => 'Выполнен',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'checklist_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['checked'], 'boolean'],
            [['name', 'checklist_id'], 'required'],
            [['checklist_id'], 'integer'],
        ];
    }

    public function fields()
    {
        return [
            'name',
            'checked' => function() {
                return $this->checked == true;
            }
        ];
    }
}
