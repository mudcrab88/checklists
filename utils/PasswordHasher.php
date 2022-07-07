<?php
namespace app\utils;

use Yii;

class PasswordHasher
{
    public function hash(string $password): string
    {
        return Yii::$app->security->generatePasswordHash($password);;
    }
}