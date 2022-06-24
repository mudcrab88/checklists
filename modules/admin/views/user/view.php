<?php

use yii\widgets\DetailView;
use app\models\User;

/**
 * @var $model User
 */

$this->title = 'Просмотр пользователя';
?>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'username',
        'email',
        'status',
        'checklists_max'
    ],
]) ?>
