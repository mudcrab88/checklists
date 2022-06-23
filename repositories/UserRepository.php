<?php
namespace app\repositories;

use app\models\User;
use yii\db\ActiveQuery;
use \Exception;

class UserRepository
{
    public function saveUser(User $user): bool
    {
        try {
            return $user->save();
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function findAllQuery(): ActiveQuery
    {
        return User::find();
    }

    public function findAll(): array
    {
        return $this->findAllQuery()->all();
    }

    public function findById($id): User
    {
        return User::findOne($id);
    }
}

