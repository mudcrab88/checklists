<?php

use yii\widgets\DetailView;
use app\models\User;
use yii\helpers\Html;

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

<div class="buttons">
    <?=
    Html::a('Просмотреть чек-листы', ['user/view-lists', 'id' => $model->id], [
        'class' => 'btn btn-warning',
    ])
    ?>
</div>
