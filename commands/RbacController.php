<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Инициализация ролей Rbac
 */
class RbacController extends Controller
{
    public function actionInit() {
        $auth = Yii::$app->authManager;

        $auth->removeAll();

        $admin = $auth->createRole('admin');
        $moderator = $auth->createRole('moderator');
        $user = $auth->createRole('user');

        $auth->add($admin);
        $auth->add($moderator);
        $auth->add($user);

        $updateAdmin = $auth->createPermission('updateAdmin');
        $updateAdmin->description = 'Редактирование администратора';
        $viewAdmin = $auth->createPermission('viewAdmin');
        $viewAdmin->description = 'Просмотр администратора';

        $updateModerator = $auth->createPermission('updateModerator');
        $updateModerator->description = 'Редактирование модератора';
        $viewModerator = $auth->createPermission('viewModerator');
        $viewModerator->description = 'Просмотр модератора';

        $updateUser = $auth->createPermission('updateUser');
        $updateUser->description = 'Редактирование пользователя';
        $viewUser = $auth->createPermission('viewUser');
        $viewUser->description = 'Просмотр пользователя';

        $auth->add($updateUser);
        $auth->add($viewUser);
        $auth->add($updateModerator);
        $auth->add($viewModerator);
        $auth->add($updateAdmin);
        $auth->add($viewAdmin);

        $auth->addChild($user, $viewUser);
        $auth->addChild($moderator, $user);
        $auth->addChild($moderator, $updateUser);
        $auth->addChild($admin, $moderator);
        $auth->addChild($admin, $viewModerator);
        $auth->addChild($admin, $updateModerator);
        $auth->addChild($admin, $viewAdmin);
        $auth->addChild($admin, $updateAdmin);

        $auth->assign($admin, 1);
    }
}

