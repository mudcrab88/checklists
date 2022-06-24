<?php
namespace app\services;

use Yii;
use app\models\User;
use app\repositories\ChecklistRepository;
use yii\data\ActiveDataProvider;

class ChecklistService
{
    protected ChecklistRepository $checklistRepository;

    public function __construct(ChecklistRepository $checklistRepository)
    {
        $this->checklistRepository = $checklistRepository;
    }

    public function findAll(): ?array
    {
        return $this->checklistRepository->findAll();
    }
}