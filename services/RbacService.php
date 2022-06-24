<?php

namespace app\services;

use app\models\User;
use Yii;

class RbacService
{
    public function checkPermissionForView(int $id): bool
    {
        $viewUser = (Yii::$app->user->can('viewUser') && $this->roleIsUser($id));
        $viewModerator = (Yii::$app->user->can('viewModerator') && $this->roleIsModerator($id));
        $viewAdmin = (Yii::$app->user->can('viewAdmin') && $this->roleIsAdmin($id));

        if ($viewUser || $viewModerator || $viewAdmin) {
            return true;
        }

        throw new \DomainException('Нет прав на просмотр пользователя!');
    }

    public function checkPermissionForUpdate(int $id): bool
    {
        $updateUser = (Yii::$app->user->can('updateUser') && $this->roleIsUser($id));
        $updateModerator = (Yii::$app->user->can('updateModerator') && $this->roleIsModerator($id));
        $updateAdmin = (Yii::$app->user->can('updateAdmin')&& $this->roleIsAdmin($id));

        if ($updateUser || $updateModerator || $updateAdmin) {
            return true;
        }

        throw new \DomainException('Нет прав на редактирование пользователя!');
    }

    public function roleIsAdmin(int $id): bool
    {
        $roles = Yii::$app->authManager->getRolesByUser($id);

        return array_key_exists(User::ROLE_ADMIN, $roles);
    }

    public function roleIsModerator(int $id): bool
    {
        $roles = Yii::$app->authManager->getRolesByUser($id);

        return array_key_exists(User::ROLE_MODERATOR, $roles);
    }

    public function roleIsUser(int $id)
    {
        $roles = Yii::$app->authManager->getRolesByUser($id);

        return array_key_exists(User::ROLE_USER, $roles);
    }

    public function canAddAdminRole(int $id): bool
    {
        return Yii::$app->user->can('updateAdmin') && !$this->roleIsAdmin($id);
    }

    public function canAddModeratorRole(int $id): bool
    {
        return Yii::$app->user->can('updateModerator') && !$this->roleIsModerator($id) && !$this->roleIsAdmin($id);
    }

    public function canUpdateUser(): bool
    {
        return Yii::$app->user->can('updateUser');
    }

    public function addAdminRole(int $id): void
    {
        $roleAdmin = Yii::$app->authManager->getRole(User::ROLE_ADMIN);
        Yii::$app->authManager->revokeAll($id);
        Yii::$app->authManager->assign($roleAdmin, $id);
    }

    public function addModeratorRole(int $id): void
    {
        $roleModerator = Yii::$app->authManager->getRole(User::ROLE_MODERATOR);
        Yii::$app->authManager->revokeAll($id);
        Yii::$app->authManager->assign($roleModerator, $id);
    }
}