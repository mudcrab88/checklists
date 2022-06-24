<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class SwaggerController extends Controller
{
    public function actions()
    {
        return [
            //The document preview addesss:http://api.yourhost.com/swagger/doc
            'doc' => [
                'class' => 'light\swagger\SwaggerAction',
                'restUrl' => \yii\helpers\Url::to(['swagger/api'], true),
            ],
            //The resultUrl action.
            'api' => [
                'class' => 'light\swagger\SwaggerApiAction',
                //The scan directories, you should use real path there.
                'scanDir' => [
                    '/app/modules/api/controllers',
                ],
            ],
        ];
    }
}