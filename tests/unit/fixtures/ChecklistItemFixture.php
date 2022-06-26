<?php
namespace tests\unit\fixtures;

use yii\test\ActiveFixture;

class ChecklistItemFixture extends ActiveFixture
{
    public $modelClass = 'app\models\ChecklistItem';
    public $depends = ['tests\unit\fixtures\ChecklistFixture'];
}