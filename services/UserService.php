<?php
namespace app\services;

use Yii;
use app\models\User;
use app\dto\UserCreateDto;
use app\repositories\UserRepository;
use yii\data\ActiveDataProvider;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function create(UserCreateDto $dto): void
    {
        $user = new User();

        $user->username = $dto->username;
        $user->email = $dto->email;
        $user->password = Yii::$app->security->generatePasswordHash($dto->password);
        $user->checklists_max = User::CHECKLISTS_MAX;
        $user->status = User::STATUS_NEW;
        $user->access_token = Yii::$app->getSecurity()->generateRandomString(32);
        $user->auth_key = Yii::$app->getSecurity()->generateRandomString(32);

        if (!$this->userRepository->saveUser($user)) {
            throw new \DomainException('Не удалось создать пользователя!');
        }

        $roleUser = Yii::$app->authManager->getRole(User::ROLE_USER);
        Yii::$app->authManager->assign($roleUser, $user->id);
    }

    public function getAllDataProvider(int $pageSize = 10): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $this->userRepository->findAllQuery(),
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);
    }

    public function findById($id): User
    {
        return $this->userRepository->findById($id);
    }

    public function updateUser(int $id): ?User
    {
        $user = $this->userRepository->findById($id);

        if ($user === null) {
            throw new \DomainException('Пользователь не найден!');
        }
        if (Yii::$app->request->isPost) {
            if ($user->load(Yii::$app->request->post()) && $this->userRepository->saveUser($user)) {
                return $user;
            } else {
                throw new \DomainException('Не удалось сохранить изменения!');
            }
        }

        return $user;
    }
}
