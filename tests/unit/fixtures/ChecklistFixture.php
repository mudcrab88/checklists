<?php
namespace tests\unit\fixtures;

use yii\test\ActiveFixture;

class ChecklistFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Checklist';
    public $depends = ['tests\unit\fixtures\UserFixture'];
}