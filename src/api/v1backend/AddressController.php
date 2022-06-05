<?php

namespace tsmd\address\api\v1backend;

use tsmd\base\models\TsmdResult;
use tsmd\address\models\Address;
use tsmd\address\models\AddressQuery;

/**
 * 提供地址、仓库管理等接口
 */
class AddressController extends \tsmd\base\controllers\RestBackendController
{
    /**
     * 获取地址列表
     *
     * <kbd>API</kbd> <kbd>GET</kbd> <kbd>AUTH</kbd> `/address/v1backend/address/search`
     *
     * Argument | Type | Required | Description
     * -------- | ---- | -------- | -----------
     * mixed    | [[string]] | No | 搜索关键，可搜索用戶 UID、手機號碼、身份證字段、收件人姓名等
     *
     * @param string $mixed
     * @return array
     */
    public function actionSearch($mixed = '')
    {
        $query = new AddressQuery();
        $query->andWhereMixed($mixed);
        $count = $query->count();
        $rows  = $query->addPaging()->orderBy('addrid DESC')->allWithFormat();
        return TsmdResult::response($rows, ['count' => $count]);
    }

    /**
     * 查看地址
     *
     * <kbd>API</kbd> <kbd>GET</kbd> <kbd>AUTH</kbd> `/address/v1backend/address/view`
     *
     * Argument | Type | Required | Description
     * -------- | ---- | -------- | -----------
     * addrid   | [[string]] | Yes | 地址ID
     *
     * @param int $addrid
     * @return array
     */
    public function actionView(int $addrid)
    {
        return TsmdResult::responseModel($this->findModel($addrid)->toArray());
    }

    /**
     * 更新地址
     *
     * <kbd>API</kbd> <kbd>POST</kbd> <kbd>AUTH</kbd> `/address/v1backend/address/update`
     *
     * Argument | Type | Required | Description
     * -------- | ---- | -------- | -----------
     * addrid   | [[string]] | Yes | 地址ID
     *
     * @return array|Address
     */
    public function actionUpdate()
    {
        $model = $this->findModel($this->getBodyParams('addrid'));
        $model->load($this->getBodyParams(), '');
        $model->update();
        return $model->hasErrors()
            ? TsmdResult::failed($model->firstErrors)
            : TsmdResult::responseModel($model->toArray());
    }

    /**
     * @param int $addrid
     * @return Address the loaded model
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $addrid)
    {
        if (($model = Address::findOne(['addrid' => $addrid])) !== null) {
            $model->findFormat();
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('The requested `address` does not exist.');
        }
    }
}
