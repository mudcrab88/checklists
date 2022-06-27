<?php

use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\helpers\Html;

/**
 * @var $listProvider ActiveDataProvider
 */
$this->title = 'Просмотр чек-листов пользователя';
?>
<?=
    GridView::widget([
        'dataProvider' => $listProvider,
        'columns'      => [
            'id',
            'name',
            'actions' => [
                'format' => 'raw',
                'value' => function( $model ) {
                    return  Html::a('Просмотреть пункты', ['/admin/checklist/view-items', 'id' => $model->id], [
                        'class' => 'btn btn-sm btn-primary',
                    ]);
                }
            ]
        ]
    ]);
?>

