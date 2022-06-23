<?php

use app\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

/**
 * @var User $model
 * @var bool $updateAdmin
 * @var bool $updateModerator
 */

$this->title = 'Обновление пользователя';

?>
<div class="buttons">
    <?= $updateAdmin
        ?
        Html::a('Сделать администратором', ['user/add-admin', 'id' => $model->id], [
            'class' => 'btn btn-danger',
        ])
        :
        ''
    ?>
    <?= $updateModerator
        ?
        Html::a('Сделать модератором', ['user/add-moderator', 'id' => $model->id], [
            'class' => 'btn btn-warning',
        ])
        :
        ''
    ?>
</div>

<div>
    <div class="panel">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-12 col-sm-6">
                <?= $form->field($model, 'username')->textInput()->label("Логин") ?>
            </div>
            <div class="col-12 col-sm-6">
                <?= $form->field($model, 'email')->textInput()->label("E-mail") ?>
            </div>
            <div class="col-12 col-sm-6">
                <?= $form->field($model, 'checklists_max')
                    ->textInput(['type' => 'number'])->label("Максимум чек-листов") ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <?= $form->field($model, 'status')
                    ->dropDownList([
                        User::STATUS_ACTIVE => 'Активный',
                        User::STATUS_NEW   => 'Новый',
                        User::STATUS_BLOCKED   => 'Заблокированный',
                    ])->label("Статус") ?>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>