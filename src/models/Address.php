<?php

namespace tsmd\address\models;

use yii\helpers\ArrayHelper;
use tsmd\base\models\ExtrasTrait;

/**
 * This is the model class for table "address".
 *
 * @property int $addrid
 * @property int $uid
 * @property string $consignee 收件人
 * @property string $idnum 身份证字号
 * @property string $mobile 手机
 * @property string $phone 电话
 * @property string $email 邮箱
 * @property string $country 国家代码 ISO-3166-1 alpha2
 * @property string $province 省
 * @property string $city 市
 * @property string $district 县区
 * @property string $street 街道
 * @property string $housenum 门牌号
 * @property string $postcode 邮编
 * @property string $collType 代收点类型
 * @property string $collCode 代收点代号
 * @property string $brief 摘要
 * @property string $tag 标签
 * @property string $pinyin 拼音
 * @property int $status 状态
 * @property int $isPrimary 默认
 * @property int $isLocked 锁定，可以设置不同级别锁定 1 2 4 8，并用按位与判断
 * @property string|array $extras
 * @property int $createdTime
 * @property int $updatedTime
 */
class Address extends \tsmd\base\models\ArModel
{
    use ExtrasTrait;

    const STATUS_NORMAL = 10;
    const STATUS_UNUSED = 30;

    /**
     * @param string $field
     * @return AddressExtras
     */
    public function getModelExtras(string $field)
    {
        return new AddressExtras();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%address}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'addrid' => 'Addrid',
            'uid' => 'Uid',
            'consignee' => '收件人',
            'idnum' => '身份证字号',
            'mobile' => '手机',
            'phone' => '电话',
            'email' => '邮箱',
            'country' => '国家代码 ISO-3166-1 alpha2',
            'province' => '省',
            'city' => '市',
            'district' => '县区',
            'street' => '街道',
            'housenum' => '门牌号',
            'postcode' => '邮编',
            'collType' => '代收点类型',
            'collCode' => '代收点代号',
            'brief' => '摘要',
            'tag' => '标签',
            'pinyin' => '拼音',
            'status' => '状态',
            'isPrimary' => '默认',
            'isLocked' => '锁定',
            'extras' => 'Extras',
            'createdTime' => 'Created Time',
            'updatedTime' => 'Updated Time',
        ];
    }

    /**
     * @param null $key
     * @param null $default
     * @return array|mixed
     */
    public static function presetStatuses($key = null, $default = null)
    {
        $data = [
            self::STATUS_NORMAL => ['name' => '正常'],
            self::STATUS_UNUSED => ['name' => '不可用'],
        ];
        return $key === null ? $data : ArrayHelper::getValue($data, $key, $default);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['fe'] = [
            'uid', 'consignee', 'idnum', 'mobile', 'phone', 'email', 'country', 'province', 'city', 'district', 'street',
            'housenum', 'postcode', 'collType', 'collCode', 'brief', 'tag', 'pinyin', 'isPrimary', 'extras',
        ];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid'], 'required'],
            [['uid', 'status', 'isPrimary', 'isLocked'], 'integer'],

            ['consignee', 'string'],
            ['consignee', function ($attribute, $params) {
                if (preg_match('#^([A-Za-z])#', $this->consignee, $m)) {
                    $this->pinyin = $m[1];
                } else {
                    $py = new \Overtrue\Pinyin\Pinyin();
                    $py = $py->convert(mb_substr($this->consignee, 0, 1));
                    $this->pinyin = $py[0] ?? '';
                }
            }],
            ['idnum', 'string'],
            ['idnum', 'filter', 'filter' => 'strtoupper'],

            [['mobile', 'phone'], 'string'],
            ['email', 'email'],

            [['country', 'province', 'city', 'district', 'street', 'housenum', 'postcode'], 'string'],
            [['collType', 'collCode', 'brief', 'tag', 'pinyin'], 'string'],

            ['extras', 'default', 'value' => []],
            ['extras', 'validateExtras'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function saveInput()
    {
        parent::saveInput();

        if (is_array($this->extras)) {
            $this->extras = json_encode($this->extras) ?: $this->extras;
        }
        // 如果设置了默认地址，须将其它地址设置为非默认状态
        if ($this->isPrimary) {
            static::updateAll(
                ['isPrimary' => 0],
                ['and', ['uid' => $this->uid, 'isPrimary' => 1], ['!=', 'addrid', $this->addrid]]
            );
        }
    }

    /**
     * 查询后的格式化处理
     */
    public function findFormat()
    {
        if (is_string($this->extras)) {
            $this->extras = $this->extras ? json_decode($this->extras, true) : [];
        }
    }

    /**
     * 獲取完整地址
     * @return string
     */
    public function getFullAddress()
    {
        $fields = ['province', 'city', 'district', 'street', 'housenum'];
        return implode('', $this->toArray($fields));
    }

    /**
     * 鎖定的地址無法刪除
     *
     * @return false|int
     */
    public function delete()
    {
        if ($this->isLocked) {
            $this->addError('AddressLocked', 'Locked address can not be deleted.');
            return false;
        }
        return parent::delete();
    }

    /**
     * 格式化处理
     * @param array $row
     */
    public static function formatBy(array &$row)
    {
        if (is_string($row['extras'])) {
            $row['extras'] = $row['extras'] ? json_decode($row['extras'], true) : [];
        }
        if (isset($row['province'])) {
            $row['fullAddress'] = $row['province'] . $row['city'] . $row['district'] . $row['street'] . $row['housenum'];
        }
    }
}
