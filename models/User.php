<?php

namespace app\models;

use yii\web\IdentityInterface;
use yii\db\ActiveRecord;

/**
 * Class User
 *
 * @property int    $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $auth_key
 * @property string $status
 * @property int    $checklists_max
 * @property string $access_token
 */

class User extends ActiveRecord implements IdentityInterface
{
    public const STATUS_BLOCKED = 'blocked';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_NEW = 'new';

    public const ROLE_USER = 'user';
    public const ROLE_MODERATOR = 'moderator';
    public const ROLE_ADMIN = 'admin';

    public const CHECKLISTS_MAX = 10;

    public function attributeLabels(): array
    {
        return [
            'id'             => 'ID',
            'username'       => 'Логин',
            'email'          => 'E-mail',
            'password'       => 'Пароль',
            'auth_key'       => 'Ключ авторизации',
            'checklists_max' => 'Количество чек-листов',
            'status'         => 'Статус',
            'access_token'   => 'Токен доступа',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password', 'auth_key', 'auth_key'], 'string'],
            [['username', 'email', 'password'], 'required'],
            [['checklists_max'], 'integer'],
            [['status'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        return $this->role;
    }

    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($auth_key)
    {
        return $this->auth_key === $auth_key;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password);
    }

    public function fields()
    {
        return [
            'username',
            'access_token'
        ];
    }

    public function getChecklists()
    {
        return $this->hasMany(Checklist::class, ['user_id' => 'id']);
    }
}
