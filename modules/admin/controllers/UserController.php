<?php
namespace app\modules\admin\controllers;

use app\services\ChecklistService;
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

    protected ChecklistService $listService;

    public function __construct(
        $id,
        $module,
        UserService $userService,
        RbacService $rbacService,
        ChecklistService $listService,
        $config = []
    )
    {
        $this->userService = $userService;
        $this->rbacService = $rbacService;
        $this->listService = $listService;

        parent::__construct($id, $module, $config);
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        if (!$this->rbacService->checkForAdminPanel()) {
            $this->redirect(['/site/index']);
        }
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

        $this->redirect(['/site/index']);
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

    /**
     * Просмотр чек-листов пользователя
     *
     * @return string
     */
    public function actionViewLists(int $id)
    {
        try {
            $permission = $this->rbacService->checkPermissionForView($id);
        } catch (\DomainException $e) {
            return $this->renderError($e);
        }

        if ($permission === true) {
            $listProvider = $this->listService->getAllByUserIdDataProvider($id);

            return $this->render(
                'view-lists',
                [
                    'listProvider' => $listProvider
                ]
            );
        }

        $this->redirect(['/site/index']);
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