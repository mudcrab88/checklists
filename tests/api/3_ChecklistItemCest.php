<?php

class ChecklistItemCest
{
    private string $authorization = 'Basic YWRtaW46JDJ5JDEzJEthcWlnbTVQUEU3UVFFMVVBMmVMcnVkdUxWckZ3ZXlQOTlZbkhNTkt2SFpXTXVCckwxOFhl';

    public function createTest(ApiTester $I)
    {
        $data = [
            'name' => 'Новый пункт',
            'checklist_id' => 1
        ];
        $I->sendPost('/checklist-item/create', $data);
        $I->seeResponseCodeIs(401);

        $I->haveHttpHeader('Authorization', $this->authorization);
        $I->sendPost('/checklist-item/create', $data);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"name":"Новый пункт"');
        $I->seeResponseCodeIsSuccessful();
    }

    public function deleteTest(ApiTester $I)
    {
        $I->sendGet('/checklist-item/delete/2');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();

        $I->haveHttpHeader('Authorization', $this->authorization);
        $I->sendGet('/checklist-item/delete/2');
        $I->seeResponseCodeIs(405);

        $I->sendDelete('/checklist-item/delete/2');
        $I->seeResponseCodeIsSuccessful();
    }

    public function setChecked(ApiTester $I)
    {
        $I->sendGet('/checklist-item/set-checked/1');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();

        $I->haveHttpHeader('Authorization', $this->authorization);
        $I->sendGet('/checklist-item/set-checked/1');
        $I->seeResponseCodeIs(405);

        $I->sendPost('/checklist-item/set-checked/1');
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseContains('"checked":true');

        $I->sendPost('/checklist-item/set-checked/1');
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseContains('"checked":false');
    }
}
