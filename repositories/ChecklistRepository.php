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
}
