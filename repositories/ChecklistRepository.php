<?php
namespace app\repositories;

use app\models\Checklist;
use yii\db\ActiveQuery;
use \Exception;

class ChecklistRepository
{
    public function findAllQuery(): ActiveQuery
    {
        return Checklist::find();
    }

    public function findAll(): array
    {
        return $this->findAllQuery()->all();
    }

    public function findOneByCondition(array $condition): ?Checklist
    {
        return Checklist::findOne($condition);
    }

    public function findAllByCondition(array $condition): ?array
    {
        return Checklist::findAll($condition);
    }

    public function findById($id): ?Checklist
    {
        return Checklist::findOne($id);
    }

    public function saveList(Checklist $list): bool
    {
        try {
            return $list->save();
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function deleteList($id)
    {
        return Checklist::deleteAll(['id' => $id]);
    }
}
