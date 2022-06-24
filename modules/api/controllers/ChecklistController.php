<?php

namespace app\modules\api\controllers;

use app\exceptions\ChecklistNotSavedException;
use app\exceptions\UserNotFoundException;
use app\exceptions\UserNotMatchException;
use app\exceptions\ChecklistNotFoundException;
use app\models\User;
use app\services\ChecklistService;
use app\services\UserService;
use yii\rest\Controller;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\AccessControl;
use OpenApi\Annotations as OA;
use yii\filters\VerbFilter;
use Yii;

/**
 * @OA\Tag(name="Чек-листы", description = "API для работы с чек-листами"),
 * @OA\SecurityScheme(
 *     securityScheme="token",
 *     type="apiKey",
 *     name="Authorization",
 *     in="header"
 * )
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

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'create' => ['post', 'put'],
                'delete' => ['post', 'delete'],
                'get-all' => ['get'],
                'get-by-user' => ['get'],
            ]
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => ['get-all', 'get-by-user'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['get-all'],
                    'roles' => [User::ROLE_ADMIN],
                ],
                [
                    'allow' => true,
                    'actions' => ['get-by-user', 'create', 'delete'],
                    'roles' => [User::ROLE_USER],
                ],
            ],
        ];

        return $behaviors;
    }

    /**
     * @OA\Get (
     *     tags={"Получение всех чек-листов"},
     *     description="Получение всех чек-листов",
     *     security={{"token": {}}},
     *     path="/api/checklist/get-all",
     *     @OA\Response(
     *         response="200",
     *         description="Список чек-листов получен",
      *       @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                  @OA\Property(property="name", description="Название чек-листа", type="string",
     *                   example="Первый чек-лист"),
     *                  @OA\Property(property="user", description="Имя пользователя", type="string",
     *                      example="user"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Пользователь не авторизован"
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="У пользователя нет прав на это действие"
     *     ),
     *     @OA\Response(
     *         response="405",
     *         description="Используется неразрешенный метод"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Внутренняя ошибка сервера"
     *     ),
     * )
     */
    public function actionGetAll()
    {
        try {
            return $this->checklistService->findAll();
        } catch (\Throwable $e) {
            return $this->responseError(500, $e);
        }
    }

    /**
     * @OA\Get (
     *     tags={"Получение чек-листов пользователя"},
     *     description="Получение чек-листов пользователя",
     *     security={{"token": {}}},
     *     path="/api/checklist/get-by-user",
     *     @OA\Parameter(
     *          name="user_id",
     *          in="query",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Список чек-листов получен",
     *       @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                  @OA\Property(property="name", description="Название чек-листа", type="string",
     *                   example="Первый чек-лист"),
     *                  @OA\Property(property="user", description="Имя пользователя", type="string",
     *                      example="user"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Запрос не прошел проверку"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Пользователь не авторизован"
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="У пользователя нет прав на это действие "
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Пользователь не найден"
     *     ),
     *     @OA\Response(
     *         response="405",
     *         description="Используется неразрешенный метод"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Внутренняя ошибка сервера"
     *     ),
     * )
     */
    public function actionGetByUser($user_id)
    {
        try {
            $list = $this->checklistService->findByUserId($user_id);
        } catch (UserNotMatchException $e) {
            return $this->responseError(403, $e);
        } catch (UserNotFoundException $e) {
            return $this->responseError(404, $e);
        } catch (\Throwable $e) {
            return $this->responseError(500, $e);
        }
        return $list;
    }

    /**
     * @OA\Post(
     *     path="/api/checklist/create",
     *     tags={"Создание чек-листа"},
     *     security={{"token": {}}},
     *     description="Создание чек-листа",
     *     @OA\RequestBody(
     *        description="JSON, содержащий название чек-листа",
     *        @OA\JsonContent(
     *             type="object",
     *             required={"name"},
     *             @OA\Property(property="name", description="Название чек-листа", type="string",
     *     example="Первый чек-лист")
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Пользователь успешно создан",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "user"},
     *             @OA\Property(property="name", description="Название чек-листа", type="string",
     *                 example="Первый чек-лист"),
     *            @OA\Property(property="user", description="Имя пользователя", type="string",
     *                 example="user")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Запрос не прошел проверку"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Пользователь не авторизован"
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="У пользователя нет прав на это действие "
     *     ),
     *     @OA\Response(
     *         response="405",
     *         description="Используется неразрешенный метод"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Внутренняя ошибка сервера"
     *     )
     * )
     */
    public function actionCreate()
    {
        try {
            $list= $this->checklistService->createFromRequest();
            $this->setStatusCode(201);

            return $list;
        } catch (ChecklistNotSavedException $e) {
            $this->setStatusCode(400);
            return [ 'message' => $e->getMessage() ];
        } catch (\Throwable $e) {
            $this->setStatusCode(500);
            return [ 'message' => $e->getMessage() ];
        }
    }

    /**
     * @OA\Delete  (
     *     tags={"Удаление чек-листа"},
     *     description="Удаление чек-листа",
     *     security={{"token": {}}},
     *     path="/api/checklist/delete",
     *     @OA\Parameter(
     *          name="id",
     *          in="query",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Чек-лист успешно удален",
     *       @OA\JsonContent(
     *             type="object",
     *             required={"message"},
     *             @OA\Property(property="message", description="Сообщение", type="string",
     *                 example="Чек-лист успешно удален")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Не удалось удалить чек-лист"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Пользователь не авторизован"
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="У пользователя нет прав на это действие "
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Пользователь не найден"
     *     ),
     *     @OA\Response(
     *         response="405",
     *         description="Используется неразрешенный метод"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Внутренняя ошибка сервера"
     *     )
     * )
     */
    public function actionDelete($id)
    {
        try {
            if ($this->checklistService->deleteChecklist($id) == true) {
                $this->setStatusCode(200);
                return [ 'message' => 'Чек-лист успешно удален' ];
            } else {
                $this->setStatusCode(400);
                return ['message' => 'Не удалось удалить чек-лист'];
            }
        } catch (UserNotMatchException $e) {
            return $this->responseError(403, $e);
        } catch (ChecklistNotFoundException $e) {
            return $this->responseError(404, $e);
        } catch (\Throwable $e) {
            $this->setStatusCode(500);
            return [ 'message' => $e->getMessage() ];
        }
    }

    private function setStatusCode(int $code): void
    {
        Yii::$app->response->statusCode = $code;
    }

    private function responseError(int $code, \Throwable $e): array
    {
        $this->setStatusCode($code);
        return [ 'message' => $e->getMessage() ];
    }
}
