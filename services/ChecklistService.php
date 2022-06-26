<?php
namespace app\services;

use app\models\Checklist;
use Yii;
use app\models\User;
use app\repositories\UserRepository;
use app\repositories\ChecklistRepository;
use app\exceptions\UserNotFoundException;
use app\exceptions\UserNotMatchException;
use app\exceptions\ChecklistNotSavedException;
use app\exceptions\ChecklistNotFoundException;

class ChecklistService
{
    protected ChecklistRepository $checklistRepository;

    protected UserRepository $userRepository;

    public function __construct(ChecklistRepository $checklistRepository, UserRepository $userRepository)
    {
        $this->checklistRepository = $checklistRepository;
        $this->userRepository = $userRepository;
    }

    public function createFromRequest(): ?Checklist
    {
        $list= new Checklist();

        if ($list->load(Yii::$app->getRequest()->getBodyParams(), '')) {
            $list->user_id = Yii::$app->user->id;

            $user = Yii::$app->user->getIdentity();
            if (count($user->getChecklists()->all()) >= $user->checklists_max) {
                throw new ChecklistNotSavedException('Превышено максимальное количество чек-листов для пользователя');
            }

            if (!$this->checklistRepository->saveList($list)) {
                throw new ChecklistNotSavedException('Не удалось сохранить чек-лист!');
            }

            return $list;
        }

        throw new ChecklistNotSavedException('Не удалось сохранить пользователя!');
    }

    public function deleteChecklist(int $id): bool
    {
        $list = $this->checklistRepository->findById($id);
        if ($list == null) {
            throw new ChecklistNotFoundException('Чек-лист не найден');
        }

        $user = $list->getUser();
        if ($user == null) {
            throw new UserNotFoundException('Пользователь не найден');
        }

        if ($user->id !== Yii::$app->user->id) {
            throw new UserNotMatchException('Создатель чек-листа не совпадает с текущим пользователем или текущий пользователь блокирован');
        }

        if ($this->checklistRepository->deleteList($id) > 0) {
            return true;
        }

        return false;
    }

    public function findAll(): ?array
    {
        return $this->checklistRepository->findAll();
    }

    public function findByUserId($user_id): ?array
    {
        $user =  $this->userRepository->findById($user_id);

        if ($user == null) {
            throw new UserNotFoundException('Пользователь не найден');
        }

        if ($user->id !== Yii::$app->user->id) {
            throw new UserNotMatchException('Пользователь не совпадает с текущим или текущий пользователь блокирован');
        }

        return $this->checklistRepository->findAllByCondition([
            'user_id' => $user_id
        ]);
    }
}