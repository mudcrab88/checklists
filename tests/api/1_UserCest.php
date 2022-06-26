<?php

class UserCest
{
    public function createUserTest(ApiTester $I)
    {
        $data = [
            'username' => 'user1',
            'email'    => 'user1@user.local',
            'password' => 'password'
        ];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost('/user/create', $data);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContains('"username":"user1"');


        $I->sendPost('/user/create', $data);
        $I->seeResponseCodeIs(400);
    }
}
