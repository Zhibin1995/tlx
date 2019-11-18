<?php

namespace common\models\app;

use common\helpers\ArrayHelper;
use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "goods".
 *
 * @property int $id
 * @property int $category_id 分类
 * @property string $name 名称
 * @property string $url 图片
 * @property string $desc 简介
 * @property string $price 价格
 * @property string $old_price 原价
 * @property string $spec 规格
 * @property string $detail 详情
 * @property int $sale_num 销量
 * @property int $is_hot 是否推荐
 * @property int $status
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at
 */
class Goods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'sale_num', 'is_hot', 'status', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['price', 'old_price'], 'number'],
            [['detail','times'], 'string'],
            [['name', 'spec'], 'string', 'max' => 255],
            [['url'], 'safe'],
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
            'category_id' => '分类',
            'name' => '名称',
            'url' => '图片',
            'desc' => '简介',
            'price' => '价格',
            'old_price' => '原价',
            'spec' => '规格',
            'detail' => '详情',
            'sale_num' => '销量',
            'times' => '耗时',
            'is_hot' => '是否推荐',
            'status' => 'Status',
            'sort' => '排序',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
    }
    /**
     * @param array $filter
     * @return array
     */
    public static function getSelectOptions($filter=[])
    {
        $items = Goods::find()->filterWhere($filter)->orderBy('sort asc')->all();
        return ArrayHelper::map($items, 'id', 'name');
    }
}
