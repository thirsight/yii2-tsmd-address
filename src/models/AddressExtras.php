<?php

namespace tsmd\address\models;

use yii\base\Model;

/**
 * address 下的 extras 字段值模型
 */
class AddressExtras extends Model
{
    /**
     * @var int 公司地址，1/0
     */
    public $isCompany;
    /**
     * @var int 偏远地址，1/0
     */
    public $isFaraway;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['isCompany', 'isFaraway'], 'number'],
            [['isCompany', 'isFaraway'], 'in', 'range' => [0, 1]],
        ];
    }
}
