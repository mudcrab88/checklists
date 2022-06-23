<?php
namespace app\modules\admin\controllers;

use yii\web\Controller;
use app\services\UserService;
use app\services\RbacService;

/**
 * User controller for the `admin` module
 */
class UserController extends Controller
{
    protected UserService $userService;
    protected RbacService $rbacService;

    public function __construct($id, $module, UserService $userService, RbacService $rbacService, $config = [])
    {
        $this->userService = $userService;
        $this->rbacService = $rbacService;

        parent::__construct($id, $module, $config);
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $usersProvider = $this->userService->getAllDataProvider();

        return $this->render(
            'index',
            [
                'usersProvider' => $usersProvider
            ]
        );
    }

    /**
     * Просмотр пользователя
     *
     * @return string
     */
    public function actionView($id): string
    {
        try {
            $permission = $this->rbacService->checkPermissionForView($id);
        } catch (\DomainException $e) {
            return $this->renderError($e);
        }

        if ($permission === true) {
            $model = $this->userService->findById($id);

            return $this->render('view', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Редактирование пользователя
     *
     * @return string
     */
    public function actionUpdate(int $id)
    {
        try {
            $permission = $this->rbacService->checkPermissionForUpdate($id);

            if ($permission === true) {
                $model = $this->userService->updateUser($id);
                $updateAdmin = $this->rbacService->canAddAdminRole($id);
                $updateModerator = $this->rbacService->canAddModeratorRole($id);

                return $this->render('update', [
                    'model' => $model,
                    'updateAdmin' => $updateAdmin,
                    'updateModerator' => $updateModerator
                ]);
            }
        } catch (\DomainException $e) {
            return $this->renderError($e);
        }
    }

    /**
     * Добавление роли администратора
     *
     * @return string
     */
    public function actionAddAdmin(int $id)
    {
        try {
            $permission = $this->rbacService->checkPermissionForUpdate($id);

            if ($permission === true) {
                $this->rbacService->addAdminRole($id);
            }

             $this->redirect(['user/index']);
        } catch (\Exception $e) {
            return $this->renderError($e);
        }
    }

    /**
     * Добавление роли модератора
     *
     * @return string
     */
    public function actionAddModerator(int $id)
    {
        try {
            $permission = $this->rbacService->checkPermissionForUpdate($id);

            if ($permission === true) {
                $this->rbacService->addModeratorRole($id);
            }

            $this->redirect(['user/index']);
        } catch (\Exception $e) {
            return $this->renderError($e);
        }
    }

    public function renderError(\Throwable $e)
    {
        return $this->render(
            'error',
            [
                'message' => $e->getMessage()
            ]
        );
    }
}