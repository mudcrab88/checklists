<?php

use yii\grid\GridView;
use yii\data\ActiveDataProvider;

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
            'user_id'
        ]
    ]);
?>

