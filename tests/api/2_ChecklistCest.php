<?php

class ChecklistCest
{
    public function createChecklistTest(ApiTester $I)
    {
        $data = [
            'name' => 'Новый чек-лист'
        ];
        $I->sendPost('/checklist/create', $data);
        $I->seeResponseCodeIs(401);

        $I->haveHttpHeader('Authorization', 'Basic YWRtaW46JDJ5JDEzJEthcWlnbTVQUEU3UVFFMVVBMmVMcnVkdUxWckZ3ZXlQOTlZbkhNTkt2SFpXTXVCckwxOFhl');
        $I->sendPost('/checklist/create', $data);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"name":"Новый чек-лист"');
        $I->seeResponseCodeIsSuccessful();

        $I->sendPost('/checklist/create', $data);
        $I->seeResponseCodeIs(400);
    }

    public function getAllTest(ApiTester $I)
    {
        $I->sendGet('/checklist/get-all');
        $I->seeResponseCodeIs(401);

        $I->haveHttpHeader('Authorization', 'Basic YWRtaW46JDJ5JDEzJEthcWlnbTVQUEU3UVFFMVVBMmVMcnVkdUxWckZ3ZXlQOTlZbkhNTkt2SFpXTXVCckwxOFhl');
        $I->sendPost('/checklist/get-all');
        $I->seeResponseCodeIs(405);

        $I->sendGet('/checklist/get-all');
        $I->seeResponseCodeIsSuccessful();
    }

    public function getByUserTest(ApiTester $I)
    {
        $I->sendGet('/checklist/get-by-user/1');
        $I->seeResponseCodeIs(401);

        $I->haveHttpHeader('Authorization', 'Basic YWRtaW46JDJ5JDEzJEthcWlnbTVQUEU3UVFFMVVBMmVMcnVkdUxWckZ3ZXlQOTlZbkhNTkt2SFpXTXVCckwxOFhl');
        $I->sendGet('/checklist/get-by-user/1');
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
    }

    public function deleteTest(ApiTester $I)
    {
        $I->sendGet('/checklist/delete/3');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();

        $I->haveHttpHeader('Authorization', 'Basic YWRtaW46JDJ5JDEzJEthcWlnbTVQUEU3UVFFMVVBMmVMcnVkdUxWckZ3ZXlQOTlZbkhNTkt2SFpXTXVCckwxOFhl');
        $I->sendGet('/checklist/delete/3');
        $I->seeResponseCodeIs(405);

        $I->sendDelete('/checklist/delete/3');
        $I->seeResponseCodeIsSuccessful();
    }
}
