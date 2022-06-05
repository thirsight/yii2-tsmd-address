<?php

namespace tsmd\address\api\v1frontend;

use tsmd\base\models\TsmdResult;
use tsmd\address\models\Address;
use tsmd\address\models\AddressQuery;

/**
 * 提供用户地址的列表、添加、查看、修改等接口
 */
class AddressController extends \tsmd\base\controllers\RestFrontendController
{
    /**
     * 用户收货地址
     *
     * <kbd>API</kbd> <kbd>GET</kbd> <kbd>AUTH</kbd> `/address/v1frontend/address/search`
     *
     * Argument | Type | Required | Description
     * -------- | ---- | -------- | -----------
     * tag       | [[string]]  | No  | tag
     * pinyin    | [[string]]  | No  | 拼音查询
     * page      | [[string]]  | No  | 分页参数，第几页，默认 1
     * pageSize  | [[string]]  | No  | 分页参数，每页多少条，默认 20
     *
     * @return array
     */
    public function actionSearch($tag = null, $pinyin = null)
    {
        $query = new AddressQuery();
        $query->andWhereMixed("uid{$this->user->uid}");
        $query->andFilterWhere(['tag' => $tag]);
        $query->andFilterWhere(['pinyin' => $pinyin]);

        $count = $query->count();
        $rows  = $query->addPaging()->orderBy('addrid DESC')->allWithFormat();
        return TsmdResult::response($rows, ['count' => $count]);
    }

    /**
     * 新增收货地址
     *
     * <kbd>API</kbd> <kbd>POST</kbd> <kbd>AUTH</kbd> `/address/v1frontend/address/create`
     *
     * @return array
     */
    public function actionCreate()
    {
        $post = $this->getBodyParams();
        $post['uid'] = $this->user->uid;

        $model = new Address();
        $model->scenario = 'fe';
        $model->load($post, '');
        $model->insert();
        return $model->hasErrors()
            ? TsmdResult::failed($model->firstErrors)
            : TsmdResult::responseModel($model->toArray());
    }

    /**
     * 查看收货地址
     *
     * <kbd>API</kbd> <kbd>GET</kbd> <kbd>AUTH</kbd> `/address/v1frontend/address/view`
     *
     * Argument | Type | Required | Description
     * -------- | ---- | -------- | -----------
     * addrid   | [[integer]] | Yes | 地址ID
     *
     * @param int $addrid
     * @return array
     */
    public function actionView(int $addrid)
    {
        return TsmdResult::responseModel($this->findModel($addrid)->toArray());
    }

    /**
     * 修改收货地址
     *
     * <kbd>API</kbd> <kbd>POST</kbd> <kbd>AUTH</kbd> `/address/v1frontend/address/update`
     *
     * @return array
     */
    public function actionUpdate()
    {
        $model = $this->findModel($this->getBodyParams('addrid'));
        $model->scenario = 'fe';
        $model->load($this->getBodyParams(), '');
        $model->update();
        return $model->hasErrors()
            ? TsmdResult::failed($model->firstErrors)
            : TsmdResult::responseModel($model->toArray());
    }

    /**
     * 刪除地址
     *
     * <kbd>API</kbd> <kbd>POST</kbd> <kbd>AUTH</kbd> `/address/v1frontend/address/delete`
     *
     * Argument | Type | Required | Description
     * -------- | ---- | -------- | -----------
     * addrid   | [[integer]] | Yes | 地址ID
     *
     * @return array|Address
     */
    public function actionDelete()
    {
        $model = $this->findModel($this->getBodyParams('addrid'));
        $model->delete();
        return $model->hasErrors()
            ? TsmdResult::failed($model->firstErrors)
            : TsmdResult::response();
    }

    /**
     * @param integer $addrid
     * @return Address the loaded model
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $addrid)
    {
        $cond = [
            'addrid' => $addrid,
            'uid' => $this->user->uid,
        ];
        if (($model = Address::findOne($cond)) !== null) {
            $model->findFormat();
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('The requested `address` does not exist.');
        }
    }
}
