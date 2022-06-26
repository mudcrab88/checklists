<?php

use yii\helpers\Html;

?>
<div class="admin-default-index">
    <h1>Панель управления. Главная страница</h1>
    <ul class="list-group">
        <li class="list-group-item">
            <?= Html::a( 'Управление пользователями', [
                '/admin/user/index'
            ], [
                'class' => 'btn btn-link'
            ])?>
        </li>
    </ul>
</div>
