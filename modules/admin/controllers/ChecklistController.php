<?php
namespace app\modules\admin\controllers;

use app\services\ChecklistService;
use app\services\ChecklistItemService;
use yii\web\Controller;

/**
 * Checklist controller for the `admin` module
 */
class ChecklistController extends Controller
{
    protected ChecklistItemService $itemService;
    protected ChecklistService $listService;

    public function __construct(
        $id,
        $module,
        ChecklistService $listService,
        ChecklistItemService $itemService,
        $config = []
    )
    {
        $this->itemService = $itemService;
        $this->listService = $listService;

        parent::__construct($id, $module, $config);
    }
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Просмотр пунктов чек-листа
     *
     * @return string
     */
    public function actionViewItems(int $id)
    {
        $itemProvider = $this->itemService->getAllByListIdDataProvider($id);

        return $this->render(
            'view-items',
            [
                'itemProvider' => $itemProvider
            ]
        );
    }
}