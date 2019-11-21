<?php

namespace common\models\app;

use Yii;

/**
 * This is the model class for table "sys_set".
 *
 * @property int $id
 * @property string $about 关于我们
 * @property string $category_bannr 分类banner
 * @property int $status
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at
 */
class SysSet extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sys_set';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['about'], 'string'],
            [['status', 'sort', 'serve_num', 'created_at', 'updated_at'], 'integer'],
            [['category_bannr'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'about' => '关于我们',
            'category_bannr' => '分类banner',
            'serve_num' => '服务人数',
            'status' => 'Status',
            'sort' => '排序',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
