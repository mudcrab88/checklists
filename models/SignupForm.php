<?php
namespace app\models;

use yii\base\Model;
use app\dto\UserCreateDto;

class SignupForm extends Model{

    public $username;
    public $email;
    public $password;

    public function rules() {
        return [
            [['username', 'email', 'password'], 'required', 'message' => 'Заполните поле'],
        ];
    }

    public function attributeLabels() {
        return [
            'username' => 'Логин',
            'email' => 'E-mail',
            'password' => 'Пароль',
        ];
    }

    public function getCreateDto(): UserCreateDto
    {
        $dto = new UserCreateDto();

        $dto->username = $this->username;
        $dto->email = $this->email;
        $dto->password =$this->password;

        return $dto;
    }

}