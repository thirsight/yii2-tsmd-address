<?php

/**
 * Be 地址接口测试
 *
 * ```
 * $ cd .../yii2-app-advanced/api # (the dir with codeception.yml)
 * $ ./codecept run api -g beAddress -d
 * $ ./codecept run api -c codeception-sandbox.yml -g beAddress -d
 * $ ./codecept run api ../vendor/thirsight/yii2-tsmd-address/tests/api/v1backend/BeAddressCest -d
 * $ ./codecept run api ../vendor/thirsight/yii2-tsmd-address/tests/api/v1backend/BeAddressCest[:xxx] -d
 * ```
 */
class BeAddressCest
{
    /**
     * @var int
     */
    public $addrid;

    /**
     * @return string[]
     */
    public function _fixtures()
    {
        return [
            'users' => 'tsmd\base\tests\fixtures\UsersFixture',
        ];
    }

    /**
     * @group beAddress
     * @group beAddressSearch
     */
    public function trySearch(ApiTester $I)
    {
        $url = $I->grabFixture('users')->wrapUrl('/address/v1backend/address/search', 'be');
        $I->sendGET($url, ['mixed' => '18800001234']);
        $I->seeResponseContains('SUCCESS');

        $resp = $I->grabResponse();
        $this->addrid = json_decode($resp, true)['list'][0]['addrid'] ?? 0;
    }

    /**
     * @group beAddress
     * @group beAddressView
     */
    public function tryView(ApiTester $I)
    {
        $url = $I->grabFixture('users')->wrapUrl('/address/v1backend/address/view', 'be');
        $I->sendGET($url, $data = ['addrid' => $this->addrid ?: 100001]);
        $I->seeResponseContainsJson($data);
    }

    /**
     * @group beAddress
     * @group beAddressUpdate
     */
    public function tryUpdate(ApiTester $I)
    {
        $data = [
            'addrid' => $this->addrid ?: 100001,
            'isLocked' => '1',
        ];
        $url = $I->grabFixture('users')->wrapUrl('/address/v1backend/address/update', 'be');
        $I->sendPOST($url, $data);
        $I->seeResponseContainsJson($data);
    }
}
