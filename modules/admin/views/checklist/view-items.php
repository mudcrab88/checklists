<?php

use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/**
 * @var $itemProvider ActiveDataProvider
 */
$this->title = 'Просмотр пунктов чек-листа';
?>
<?=
GridView::widget([
    'dataProvider' => $itemProvider,
    'columns'      => [
        'id',
        'name',
        'checked' => [
            'attribute' => 'checked',
            'value' => function($model) {
                return $model->checked ? 'Да' : 'Нет';
            }
        ]
    ]
]);
?>