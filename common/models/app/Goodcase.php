<?php

namespace common\models\app;

use Yii;

/**
 * This is the model class for table "goodcase".
 *
 * @property int $id
 * @property string $name 名称
 * @property string $url 图片
 * @property string $address 地址
 * @property string $video_url 视频
 * @property int $status
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at
 */
class Goodcase extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'goodcase';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['url'], 'string', 'max' => 1024],
            [['address'], 'string', 'max' => 511],
            [['video_url'], 'string', 'max' => 1023],
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
            'address' => '地址',
            'video_url' => '视频',
            'status' => 'Status',
            'sort' => '排序',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
    }
}
