<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\grid\ActionColumn;

/**
 * @var $usersProvider ActiveDataProvider
 */

$this->title = 'Просмотр списка пользователей';
?>
<?=
    GridView::widget([
        'dataProvider' => $usersProvider,
        'columns' => [
            'id',
            'username',
            'email',
            'checklists_max',
            [
                'class'    => ActionColumn::class,
                'template' => '{view} {update}',
            ],
        ]
    ]);
?>
