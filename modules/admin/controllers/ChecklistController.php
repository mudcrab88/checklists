<?php
namespace app\modules\admin\controllers;

use yii\web\Controller;

/**
 * Checklist controller for the `admin` module
 */
class ChecklistController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}