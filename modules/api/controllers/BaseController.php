<?php

namespace app\modules\api\controllers;

use yii\rest\Controller;
use Yii;


class BaseController extends Controller
{
    protected function setStatusCode(int $code): void
    {
        Yii::$app->response->statusCode = $code;
    }

    protected function responseError(int $code, \Throwable $e): array
    {
        $this->setStatusCode($code);
        return [ 'message' => $e->getMessage() ];
    }

    protected function responseWithCode(int $code, string $message): array
    {
        $this->setStatusCode($code);
        return [ 'message' => $message ];
    }
}
