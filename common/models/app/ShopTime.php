<?php

namespace common\models\app;

use Yii;

/**
 * This is the model class for table "shop_time".
 *
 * @property int $id
 * @property int $shop_id 商户
 * @property int $date 日期
 * @property int $start_time 开始时间
 * @property int $end_time 结束时间
 * @property int $is_use 是否使用
 * @property int $status
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at
 */
class ShopTime extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shop_time';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shop_id', 'date', 'start_time', 'end_time', 'is_use', 'status', 'sort', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_id' => '商户',
            'date' => '日期',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'is_use' => '是否使用',
            'status' => 'Status',
            'sort' => '排序',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
    }
}
