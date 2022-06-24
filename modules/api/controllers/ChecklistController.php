<?php

namespace app\modules\api\controllers;

use app\models\Checklist;
use app\services\ChecklistService;
use app\services\UserService;
use yii\rest\Controller;
use yii\filters\auth\HttpBasicAuth;
use OpenApi\Annotations as OA;

/**
 * @OA\PathItem(path="/api/checklist"))
 */
class ChecklistController extends Controller
{
    protected ChecklistService $checklistService;

    protected UserService $userService;

    public function __construct($id, $module, ChecklistService $checklistService, UserService $userService, $config =
    [])
    {
        $this->checklistService = $checklistService;
        $this->userService = $userService;

        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::class,
            'auth' => function ($username, $password) {
                return $this->userService->findOneByCondition([
                    'username' => $username,
                    'password' => $password,
                ]);
            }
        ];
        return $behaviors;
    }

    /**
     * @OA\Get (
     *     tags={"Получение всех чек-листов"},
     *     path="/api/checklist/get-all",
     *     @OA\Response(
     *         response="200",
     *         description="Список чек-листов получен"
     *     )
     * )
     */
    public function actionGetAll()
    {
        return $this->checklistService->findAll();
    }
}
