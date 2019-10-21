<?php

namespace app\models;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "member".
 *
 * @property int $id
 * @property string $open_id openid
 * @property string $union_id unionid
 * @property string $nickname 昵称
 * @property int $gender 性别
 * @property string $city 城市
 * @property string $province 省份
 * @property string $country 国家
 * @property string $head_img 头像
 * @property string $username 名称
 * @property string $avatar 头像地址
 * @property string $userphone 手机号码
 * @property int $sex 性别
 * @property int $birth 生日
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $longitude 经度
 * @property string $latitude 纬度
 */
class Member extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'member';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gender', 'sex', 'birth', 'status', 'created_at', 'updated_at'], 'integer'],
            [['open_id', 'union_id', 'nickname', 'city', 'province', 'country', 'username', 'userphone', 'longitude', 'latitude'], 'string', 'max' => 255],
            [['head_img', 'avatar'], 'string', 'max' => 1024],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'open_id' => 'openid',
            'union_id' => 'unionid',
            'nickname' => '昵称',
            'gender' => '性别',
            'city' => '城市',
            'province' => '省份',
            'country' => '国家',
            'head_img' => '头像',
            'username' => '名称',
            'avatar' => '头像地址',
            'userphone' => '手机号码',
            'sex' => '性别',
            'birth' => '生日',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'longitude' => '经度',
            'latitude' => '纬度',
        ];
    }
}
