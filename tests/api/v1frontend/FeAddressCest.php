<?php

/**
 * Fe 地址接口测试
 *
 * ```
 * $ cd .../yii2-app-advanced/api # (the dir with codeception.yml)
 * $ ./codecept run api -g feAddress -d
 * $ ./codecept run api -c codeception-sandbox.yml -g feAddress -d
 * $ ./codecept run api ../vendor/thirsight/yii2-tsmd-address/tests/api/v1frontend/BeAddressCest -d
 * $ ./codecept run api ../vendor/thirsight/yii2-tsmd-address/tests/api/v1frontend/BeAddressCest[:xxx] -d
 * ```
 */
class FeAddressCest
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
     * @group feAddress
     * @group feAddressSearch
     */
    public function trySearch(ApiTester $I)
    {
        $url = $I->grabFixture('users')->wrapUrl('/address/v1frontend/address/search', 'fe');
        $I->sendGET($url);
        $I->seeResponseContains('SUCCESS');

        $resp = $I->grabResponse();
        $this->addrid = json_decode($resp, true)['list'][0]['addrid'] ?? 0;
    }

    /**
     * @group feAddress
     * @group feAddressCreate
     */
    public function tryCreate(ApiTester $I)
    {
        $data = [
            'country' => 'CN',
            'consignee' => '某人',
            'idnum' => '1234567890987654321',
            'mobile' => '18800001234',
            'phone' => '0755-1234567890',
            'province' => '某省',
            'city' => '某市',
            'district' => '某区',
            'housenum' => '某街道某门牌号',
            'postcode' => '123456',
            'collType' => 'cainiao',
            'collCode' => '654321',
            'isPrimary' => '1',
            'brief' => '摘要',
            'extras' => [
                'isCompany' => '1',
                'isFaraway' => '1',
            ],
        ];
        $url = $I->grabFixture('users')->wrapUrl('/address/v1frontend/address/create', 'fe');
        $I->sendPOST($url, $data);
        $I->seeResponseContainsJson(['idnum' => $data['idnum']]);

        $resp = $I->grabResponse();
        $this->addrid = json_decode($resp, true)['model']['addrid'] ?? 0;
    }

    /**
     * @group feAddress
     * @group feAddressView
     */
    public function tryView(ApiTester $I)
    {
        $url = $I->grabFixture('users')->wrapUrl('/address/v1frontend/address/view', 'fe');
        $I->sendGET($url, $data = ['addrid' => $this->addrid ?: 100001]);
        $I->seeResponseContainsJson($data);
    }

    /**
     * @group feAddress
     * @group feAddressUpdate
     */
    public function tryUpdate(ApiTester $I)
    {
        $data = [
            'addrid' => $this->addrid ?: 100001,
            'country' => 'CN',
            'isPrimary' => '0',
            'brief' => '摘要更新',
            'extras' => [
                'isCompany' => 0,
            ],
        ];
        $url = $I->grabFixture('users')->wrapUrl('/address/v1frontend/address/update', 'fe');
        $I->sendPOST($url, $data);
        $I->seeResponseContains('SUCCESS');
    }

    /**
     * @group feAddressDelete
     */
    public function tryDelete(ApiTester $I)
    {
        $data = [
            'addrid' => $this->addrid ?: 100001,
        ];
        $url = $I->grabFixture('users')->wrapUrl('/address/v1frontend/address/delete', 'fe');
        $I->sendPOST($url, $data);
        $I->seeResponseContains('"tsmdResult":"SUCCESS"');
    }
}
