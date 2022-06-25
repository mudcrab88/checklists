<?php

namespace app\modules\api\controllers;

use app\exceptions\ChecklistItemNotFoundException;
use app\exceptions\ChecklistItemNotSavedException;
use app\exceptions\UserNotFoundException;
use app\exceptions\UserNotMatchException;
use app\exceptions\ChecklistNotFoundException;
use app\models\User;
use app\services\ChecklistItemService;
use app\services\UserService;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\AccessControl;
use OpenApi\Annotations as OA;
use yii\filters\VerbFilter;
use Yii;

/**
 * @OA\Tag(name="Пункты чек-листов", description = "API для работы с пунктами чек-листов")
 */
class ChecklistItemController extends BaseController
{
    protected ChecklistItemService $itemService;

    protected UserService $userService;

    public function __construct($id, $module, ChecklistItemService $itemService, UserService $userService,
        $config = [])
    {
        $this->itemService = $itemService;

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
                'delete' => ['delete'],
                'get-items' => ['get'],
            ]
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => ['create', 'get-items'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['create', 'get-items'],
                    'roles' => [User::ROLE_USER],
                ],
            ],
        ];

        return $behaviors;
    }

    /**
     * @OA\Post(
     *     path="/api/checklist-item/create",
     *     tags={"Создание пункта чек-листа"},
     *     security={{"token": {}}},
     *     description="Создание пункта чек-листа",
     *     @OA\RequestBody(
     *        description="JSON, содержащий название пункта и id чек-листа",
     *        @OA\JsonContent(
     *             type="object",
     *             required={"name", "checklist_id"},
     *             @OA\Property(property="name", description="Название пункта чек-листа", type="string",
     *     example="Первый пункт"),
     *     @OA\Property(property="checklist_id", description="ID чек-листа", type="integer",
     *     example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Чек-лист успешно создан",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "checked"},
     *             @OA\Property(property="name", description="Название пункта чек-листа", type="string",
     *                 example="Первый пункт"),
     *            @OA\Property(property="checked", description="Выполнен", type="boolean",
     *                 example="false")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Не удалось сохранить пункт"
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
     *         response="404",
     *         description="Чек-лист не найден"
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
            $item= $this->itemService->createFromRequest();
            $this->setStatusCode(201);

            return $item;
        } catch (ChecklistItemNotSavedException $e) {
            return $this->responseError(400, $e);
        } catch (UserNotMatchException $e) {
            return $this->responseError(403, $e);
        } catch (ChecklistNotFoundException $e) {
            return $this->responseError(404, $e);
        }  catch (\Throwable $e) {
            $this->setStatusCode(500);
            return [ 'message' => $e->getMessage() ];
        }
    }

    public function actionDelete($id)
    {
        try {
            return ($this->itemService->deleteItem($id) == true)
                ?
                $this->responseWithCode(200, 'Пункт успешно удален')
                :
                $this->responseWithCode(400, 'Не удалось удалить пункт');
        }
        catch (UserNotMatchException $e) {
            return $this->responseError(403, $e);
        } catch (ChecklistNotFoundException|ChecklistItemNotFoundException $e) {
            return $this->responseError(404, $e);
        } catch (\Throwable $e) {
            return $this->responseError(500, $e);
        }
    }

    /**
     * @OA\Get (
     *     tags={"Получение списка пунктов чек-листа"},
     *     description="Получение списка пунктов чек-листа",
     *     security={{"token": {}}},
     *     path="/api/checklist-item/get-items",
     *     @OA\Parameter(
     *          name="checklist_id",
     *          in="query",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Список пунктов чек-листа получен",
     *       @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                  @OA\Property(property="name", description="Название пункта", type="string",
     *                   example="Первый пункт"),
     *                  @OA\Property(property="checked", description="Выполнен", type="boolean",
     *                      example="false"),
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
     *         description="Чек-лист не найден"
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
    public function actionGetItems($checklist_id)
    {
        try {
            $items = $this->itemService->findByChecklistId($checklist_id);
        } catch (UserNotMatchException $e) {
            return $this->responseError(403, $e);
        } catch (ChecklistNotFoundException $e) {
            return $this->responseError(404, $e);
        } catch (\Throwable $e) {
            return $this->responseError(500, $e);
        }
        return $items;
    }
}
