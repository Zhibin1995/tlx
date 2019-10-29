<?php

namespace common\models\app;

use Yii;

/**
 * This is the model class for table "make_detail".
 *
 * @property int $id
 * @property int $make_id 预约id
 * @property string $category 类型
 * @property string $brand 品牌
 * @property string $type 型号
 * @property int $buy_time 购买时间
 * @property int $last_time 上次清洗时间
 * @property string $remake 备注
 * @property int $status
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at
 */
class MakeDetail extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'make_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['make_id', 'buy_time', 'last_time', 'status', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['category', 'brand', 'type', 'remake'], 'string', 'max' => 1024],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'make_id' => '预约id',
            'category' => '类型',
            'brand' => '品牌',
            'type' => '型号',
            'buy_time' => '购买时间',
            'last_time' => '上次清洗时间',
            'remake' => '备注',
            'status' => 'Status',
            'sort' => '排序',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
    }
}
