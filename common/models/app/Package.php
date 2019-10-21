<?php

namespace app\models;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "package".
 *
 * @property int $id
 * @property string $name 名称
 * @property string $url 图片
 * @property string $price 价格
 * @property string $desc 描述
 * @property int $sale_num 销量
 * @property int $status
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at
 */
class Package extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'package';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price'], 'number'],
            [['sale_num', 'status', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['url'], 'string', 'max' => 1024],
            [['desc'], 'string', 'max' => 511],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'url' => '图片',
            'price' => '价格',
            'desc' => '描述',
            'sale_num' => '销量',
            'status' => 'Status',
            'sort' => '排序',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
    }
}
