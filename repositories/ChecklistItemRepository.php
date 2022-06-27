<?php

namespace app\repositories;

use app\models\Checklist;
use yii\db\ActiveQuery;
use \Exception;
use app\models\ChecklistItem;

class ChecklistItemRepository
{
    public function findById($id): ?ChecklistItem
    {
        return ChecklistItem::findOne($id);
    }

    public function saveItem(ChecklistItem $item): bool
    {
        try {
            return $item->save();
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function deleteItem($id): int
    {
        return ChecklistItem::deleteAll(['id' => $id]);
    }

    public function findAllByConditionQuery(array $condition): ?ActiveQuery
    {
        return ChecklistItem::find()->where($condition)->orderBy('id');
    }

    public function findAllByCondition(array $condition): ?array
    {
        return $this->findAllByConditionQuery($condition)->all();
    }
}
