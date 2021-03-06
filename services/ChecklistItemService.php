<?php
namespace app\services;


use app\exceptions\ChecklistItemNotSavedException;
use Yii;
use app\models\User;
use app\models\ChecklistItem;
use app\repositories\ChecklistItemRepository;
use app\repositories\ChecklistRepository;
use app\exceptions\ChecklistItemNotFoundException;
use app\exceptions\UserNotMatchException;
use app\exceptions\ChecklistNotFoundException;
use yii\data\ActiveDataProvider;

class ChecklistItemService
{
    protected ChecklistRepository $checklistRepository;

    protected ChecklistItemRepository $itemRepository;

    public function __construct(ChecklistRepository $checklistRepository, ChecklistItemRepository $itemRepository)
    {
        $this->checklistRepository = $checklistRepository;
        $this->itemRepository = $itemRepository;
    }

    public function findByChecklistId($checklist_id): array
    {
        $this->checkUserByListId($checklist_id);

        return $this->itemRepository->findAllByCondition([
            'checklist_id' => $checklist_id
        ]);
    }

    public function createFromRequest(): ?ChecklistItem
    {
        $item= new ChecklistItem();

        if ($item->load(Yii::$app->getRequest()->getBodyParams(), '') && $item->validate()) {
            $item->checked = false;

            $this->checkUserByListId($item->checklist_id);

            if (!$this->itemRepository->saveItem($item)) {
                throw new ChecklistItemNotSavedException('Не удалось сохранить пункт!');
            }

            return $item;
        }

        throw new ChecklistItemNotSavedException('Не удалось сохранить пункт!');
    }

    public function deleteItem(int $id): bool
    {
        $item = $this->itemRepository->findById($id);
        if ($item == null) {
            throw new ChecklistItemNotFoundException('Пункт не найден');
        }

        $this->checkUserByListId($item->checklist_id);

        if ($this->itemRepository->deleteItem($id) > 0) {
            return true;
        }

        return false;
    }

    public function setChecked(int $id): ?ChecklistItem
    {
        $item = $this->itemRepository->findById($id);
        if ($item == null) {
            throw new ChecklistItemNotFoundException('Пункт не найден');
        }

        $this->checkUserByListId($item->checklist_id);

        $item->checked = !($item->checked == true);
        if (!$this->itemRepository->saveItem($item)) {
            throw new ChecklistItemNotSavedException('Не удалось сохранить пункт!');
        }

        return $item;
    }

    protected function checkUserByListId(int $checklist_id): void
    {
        $list =  $this->checklistRepository->findById($checklist_id);
        if ($list == null) {
            throw new ChecklistNotFoundException('Чек-лист не найден');
        }

        $user = $list->getUser();
        if ($user->id !== Yii::$app->user->id) {
            throw new UserNotMatchException('Пользователь-владелец не совпадает с текущим или текущий пользователь блокирован');
        }
    }

    public function getAllByListIdDataProvider(int $list_id, int $pageSize = 10): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $this->itemRepository->findAllByConditionQuery(['checklist_id' => $list_id]),
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);
    }
}