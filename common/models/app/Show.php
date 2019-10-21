<?php

namespace app\models;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "show".
 *
 * @property int $id
 * @property string $name 名称
 * @property string $url 图片
 * @property string $address 地址
 * @property string $video_url 视频
 * @property int $digg 点赞
 * @property int $look 观看
 * @property int $status
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at
 */
class Show extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'show';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['digg', 'look', 'status', 'sort', 'created_at', 'updated_at'], 'integer'],
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
            'digg' => '点赞',
            'look' => '观看',
            'status' => 'Status',
            'sort' => '排序',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
    }
}
