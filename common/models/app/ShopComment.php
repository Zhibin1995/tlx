<?php

namespace common\models\app;

use Yii;

/**
 * This is the model class for table "shop_comment".
 *
 * @property int $id
 * @property int $shop_id 商家
 * @property int $serve 服务
 * @property int $art 规范
 * @property int $flow 流程
 * @property int $wear 着装
 * @property int $total 总分
 * @property int $status
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at
 */
class ShopComment extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shop_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shop_id', 'serve', 'art', 'flow', 'wear', 'total', 'status', 'sort', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_id' => '商家',
            'serve' => '服务',
            'art' => '规范',
            'flow' => '流程',
            'wear' => '着装',
            'total' => '总分',
            'status' => 'Status',
            'sort' => '排序',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
    }
}
