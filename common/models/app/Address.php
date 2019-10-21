<?php

namespace app\models;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "address".
 *
 * @property int $id 主键
 * @property int $member_id 用户id
 * @property int $province_id 省id
 * @property int $city_id 市id
 * @property int $area_id 区id
 * @property string $address_name 地址
 * @property string $address_details 详细地址
 * @property int $is_default 默认地址
 * @property int $zip_code 邮编
 * @property string $realname 真实姓名
 * @property string $home_phone 家庭号码
 * @property string $mobile 手机号码
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class Address extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'province_id', 'city_id', 'area_id', 'is_default', 'zip_code', 'status', 'created_at', 'updated_at'], 'integer'],
            [['address_name', 'address_details'], 'string', 'max' => 200],
            [['realname'], 'string', 'max' => 100],
            [['home_phone', 'mobile'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'member_id' => '用户id',
            'province_id' => '省id',
            'city_id' => '市id',
            'area_id' => '区id',
            'address_name' => '地址',
            'address_details' => '详细地址',
            'is_default' => '默认地址',
            'zip_code' => '邮编',
            'realname' => '真实姓名',
            'home_phone' => '家庭号码',
            'mobile' => '手机号码',
            'status' => '状态(-1:已删除,0:禁用,1:正常)',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
