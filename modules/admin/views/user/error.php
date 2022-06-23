<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception$exception */

use yii\helpers\Html;

$this->title = 'Ошибка';
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        В процессе обработки запроса произошла ошибка.
    </p>
    <p>
        Пожалуйста, сообщите нам, если это ошибка сервера. Заранее спасибо!
    </p>

</div>
