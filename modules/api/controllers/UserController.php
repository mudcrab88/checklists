<?php

namespace app\modules\api\controllers;

use app\services\UserService;
use yii\rest\Controller;
use Yii;
use OpenApi\Annotations as OA;
use app\exceptions\UserNotSavedException;

/**
 * @OA\Info(title="API для работы с чек-листами", version="1"),
 * @OA\PathItem(path="/api/user"))
 */
class UserController extends BaseController
{
    protected UserService $userService;

    public function __construct($id, $module, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $module, $config);
    }

    /**
     * @OA\Post(
     *     path="/api/user/create",
     *     tags={"Создание пользователя"},
     *     description="Создание пользователя",
     *     @OA\RequestBody(
     *        description="JSON, содержащий имя пользователя, email и пароль",
     *        @OA\JsonContent(
     *             type="object",
     *             required={"username", "email", "password"},
     *             @OA\Property(property="username", description="Имя пользователя", type="string",
     *     example="User"),
     *             @OA\Property(property="email", description="Электронная почта", type="string",
     *     example="user@mail.local"),
     *             @OA\Property(property="password", description="Пароль", type="string",
     *     example="password"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Пользователь успешно создан",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"username", "access_token"},
     *             @OA\Property(property="username", description="Имя пользователя", type="string", example="User"),
     *            @OA\Property(property="access_token", description="Токен доступа", type="string", example="bXVkY3JhYjpNYXN0ZXJrZXkx"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Запрос не прошел проверку",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"message"},
     *             @OA\Property(property="message", description="Сообщение о неудаче", type="string", example="Не
     * удалось сохранить пользователя"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="405",
     *         description="Используется неразрешенный метод"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Внутренняя ошибка сервера",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"message"},
     *             @OA\Property(property="message", description="Сообщение о внутренней ошибке сервера",
     *     type="string",  example="Internal Server Error"),
     *         )
     *     )
     * )
     */
    public function actionCreate()
    {
        try {
            $user = $this->userService->createFromRequest();
            $this->setStatusCode(201);

            return $user;
        } catch (UserNotSavedException $e) {
            $this->setStatusCode(400);
            return [ 'message' => $e->getMessage() ];
        } catch (\Throwable $e) {
            $this->setStatusCode(500);
            return [ 'message' => $e->getMessage() ];
        }
    }
}
