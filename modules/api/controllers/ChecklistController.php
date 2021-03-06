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
 * @OA\SecurityScheme(
 *     securityScheme="token",
 *     type="apiKey",
 *     name="Authorization",
 *     in="header"
 * )
 */
class ChecklistController extends BaseController
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
                    'status'   => User::STATUS_ACTIVE
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
                'get-items' => ['get'],
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
                    'actions' => ['get-by-user', 'create', 'delete', 'get-items'],
                    'roles' => [User::ROLE_USER],
                ],
            ],
        ];

        return $behaviors;
    }

    /**
     * @OA\Get (
     *     tags={"?????????????????? ???????? ??????-????????????"},
     *     description="?????????????????? ???????? ??????-????????????",
     *     security={{"token": {}}},
     *     path="/api/checklist/get-all",
     *     @OA\Response(
     *         response="200",
     *         description="???????????? ??????-???????????? ??????????????",
      *       @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                  @OA\Property(property="name", description="???????????????? ??????-??????????", type="string",
     *                   example="???????????? ??????-????????"),
     *                  @OA\Property(property="user", description="?????? ????????????????????????", type="string",
     *                      example="user"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="???????????????????????? ???? ??????????????????????"
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="?? ???????????????????????? ?????? ???????? ???? ?????? ????????????????"
     *     ),
     *     @OA\Response(
     *         response="405",
     *         description="???????????????????????? ?????????????????????????? ??????????"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="???????????????????? ???????????? ??????????????"
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
     *     tags={"?????????????????? ??????-???????????? ????????????????????????"},
     *     description="?????????????????? ??????-???????????? ????????????????????????",
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
     *         description="???????????? ??????-???????????? ??????????????",
     *       @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                  @OA\Property(property="name", description="???????????????? ??????-??????????", type="string",
     *                   example="???????????? ??????-????????"),
     *                  @OA\Property(property="user", description="?????? ????????????????????????", type="string",
     *                      example="user"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="???????????? ???? ???????????? ????????????????"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="???????????????????????? ???? ??????????????????????"
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="?? ???????????????????????? ?????? ???????? ???? ?????? ???????????????? "
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="???????????????????????? ???? ????????????"
     *     ),
     *     @OA\Response(
     *         response="405",
     *         description="???????????????????????? ?????????????????????????? ??????????"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="???????????????????? ???????????? ??????????????"
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
     *     tags={"???????????????? ??????-??????????"},
     *     security={{"token": {}}},
     *     description="???????????????? ??????-??????????",
     *     @OA\RequestBody(
     *        description="JSON, ???????????????????? ???????????????? ??????-??????????",
     *        @OA\JsonContent(
     *             type="object",
     *             required={"name"},
     *             @OA\Property(property="name", description="???????????????? ??????-??????????", type="string",
     *     example="???????????? ??????-????????")
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="??????-???????? ?????????????? ????????????",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "user"},
     *             @OA\Property(property="name", description="???????????????? ??????-??????????", type="string",
     *                 example="???????????? ??????-????????"),
     *            @OA\Property(property="user", description="?????? ????????????????????????", type="string",
     *                 example="user")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="???????????? ???? ???????????? ????????????????"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="???????????????????????? ???? ??????????????????????"
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="?? ???????????????????????? ?????? ???????? ???? ?????? ???????????????? "
     *     ),
     *     @OA\Response(
     *         response="405",
     *         description="???????????????????????? ?????????????????????????? ??????????"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="???????????????????? ???????????? ??????????????"
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
     *     tags={"???????????????? ??????-??????????"},
     *     description="???????????????? ??????-??????????",
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
     *         description="??????-???????? ?????????????? ????????????",
     *       @OA\JsonContent(
     *             type="object",
     *             required={"message"},
     *             @OA\Property(property="message", description="??????????????????", type="string",
     *                 example="??????-???????? ?????????????? ????????????")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="???? ?????????????? ?????????????? ??????-????????"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="???????????????????????? ???? ??????????????????????"
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="?? ???????????????????????? ?????? ???????? ???? ?????? ???????????????? "
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="??????-???????? ???? ????????????"
     *     ),
     *     @OA\Response(
     *         response="405",
     *         description="???????????????????????? ?????????????????????????? ??????????"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="???????????????????? ???????????? ??????????????"
     *     )
     * )
     */
    public function actionDelete($id)
    {
        try {
            return ($this->checklistService->deleteChecklist($id) == true)
                   ?
                   $this->responseWithCode(200, '??????-???????? ?????????????? ????????????')
                   :
                   $this->responseWithCode(400, '???? ?????????????? ?????????????? ??????-????????');
        }
        catch (UserNotMatchException $e) {
            return $this->responseError(403, $e);
        } catch (ChecklistNotFoundException $e) {
            return $this->responseError(404, $e);
        } catch (\Throwable $e) {
            return $this->responseError(500, $e);
        }
    }
}
