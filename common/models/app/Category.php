<?php

namespace app\models;

use common\helpers\ArrayHelper;
use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $name 名称
 * @property string $url 图片
 * @property string $banner 分类内banner
 * @property int $status
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at
 */
class Category extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['url', 'banner'], 'string', 'max' => 1024],
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
            'banner' => '分类内banner',
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
        $items = Category::find()->filterWhere($filter)->orderBy('sort asc')->all();
        return ArrayHelper::map($items, 'id', 'name');
    }
}
