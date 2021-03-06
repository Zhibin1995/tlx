<?php

namespace common\models\app;

use Yii;

/**
 * This is the model class for table "shop".
 *
 * @property int $id
 * @property string $username 用户姓名
 * @property string $position 职位
 * @property string $userphone 手机号码
 * @property string $password 密码
 * @property string $img_url 头像
 * @property int $status
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at
 * @property int $province_id 省份
 * @property int $city_id 城市
 * @property int $area_id 区域
 */
class Shop extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shop';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'sort', 'created_at', 'updated_at', 'province_id', 'city_id', 'area_id'], 'integer'],
            [['username', 'position'], 'string', 'max' => 255],
            [['userphone'], 'string', 'max' => 1024],
            [['password'], 'string', 'max' => 511],
            [['img_url'], 'string', 'max' => 1023],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户姓名',
            'position' => '职位',
            'userphone' => '手机号码',
            'password' => '密码',
            'img_url' => '头像',
            'status' => 'Status',
            'sort' => '排序',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
            'province_id' => '省份',
            'city_id' => '城市',
            'area_id' => '区域',
        ];
    }
}
