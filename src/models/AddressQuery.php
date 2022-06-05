<?php

namespace tsmd\address\models;

use Yii;
use tsmd\base\models\TsmdQueryTrait;

/**
 * This is the Query class for [[Address]].
 */
class AddressQuery extends \yii\db\Query
{
    use TsmdQueryTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->from(Address::tableName());
        $this->modelClass = Address::class;
    }

    /**
     * @return $this
     */
    public function andWhereMixed($mixed)
    {
        $mixed = Yii::$app->formatter->stripBlank($mixed);

        if (stripos($mixed, 'uid') === 0) {
            // 用戶 UID 查詢
            return $this->andWhereIn('uid', str_ireplace('uid', '', $mixed));

        } elseif (stripos($mixed, 'idnum') === 0) {
            // 身份证字号查詢
            return $this->andWhereIn('idnum', str_ireplace('idnum', '', $mixed));

        } elseif (stripos($mixed, 'mobile') === 0) {
            // 手机查詢
            return $this->andWhereIn('mobile', str_ireplace('mobile', '', $mixed));

        } elseif (is_numeric($mixed)) {
            // addrid 查詢
            return $this->andWhere(['addrid' => $mixed]);

        } elseif ($mixed) {
            // 收件人姓名查詢
            return $this->andWhere(['consignee' => $mixed]);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function allWithFormat()
    {
        $rows = $this->all();
        array_walk($rows, function (&$r) {
            Address::formatBy($r);
        });
        return $rows;
    }
}
