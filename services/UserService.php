<?php
namespace app\services;

use Yii;
use app\models\User;
use app\dto\UserCreateDto;
use app\repositories\UserRepository;
use yii\data\ActiveDataProvider;
use app\exceptions\UserNotSavedException;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createFromDto(UserCreateDto $dto): void
    {
        $user = new User();

        $user->username = $dto->username;
        $user->email = $dto->email;
        $user->password = Yii::$app->security->generatePasswordHash($dto->password);
        $user->access_token = base64_encode($dto->username.':'.$dto->password);
        $user = $this->fillDefaultFields($user);

        if (!$this->userRepository->saveUser($user)) {
            throw new \DomainException('Не удалось сохранить пользователя!');
        }

        $this->setRole($user, User::ROLE_USER);
    }

    public function createFromRequest(): ?User
    {
        $user = new User();
        if ($user->load(Yii::$app->getRequest()->getBodyParams(), '')) {
            $user->password = Yii::$app->security->generatePasswordHash($user->password);
            $user->access_token = base64_encode($user->username.':'.$user->password);
            $user = $this->fillDefaultFields($user);

            if (!$this->userRepository->saveUser($user)) {
                throw new UserNotSavedException('Не удалось сохранить пользователя!');
            }
            $this->setRole($user, User::ROLE_USER);

            return $user;
        }

        throw new UserNotSavedException('Не удалось создать пользователя!');
    }

    public function fillDefaultFields(User $user): User
    {
        $user->checklists_max = User::CHECKLISTS_MAX;
        $user->status = User::STATUS_ACTIVE;
        $user->auth_key = Yii::$app->getSecurity()->generateRandomString(32);

        return $user;
    }

    public function setRole(User $user, string $role): void
    {
        $roleUser = Yii::$app->authManager->getRole($role);
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

    public function findById($id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function findOneByCondition(array $condition): ?User
    {
        return $this->userRepository->findOneByCondition($condition);
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
