<?php

namespace app\models;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "comment_img".
 *
 * @property int $id
 * @property int $comment_Id 名称
 * @property string $url 图片
 * @property int $status
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at
 */
class CommentImg extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comment_img';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['comment_Id', 'status', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['url'], 'string', 'max' => 1024],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'comment_Id' => '名称',
            'url' => '图片',
            'status' => 'Status',
            'sort' => '排序',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
    }
}
