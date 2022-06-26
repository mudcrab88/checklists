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

    public function findAllByCondition(array $condition): ?array
    {
        return ChecklistItem::findAll($condition);
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
}
