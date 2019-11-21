<?php

namespace common\models\app;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property int $member_Id 用户
 * @property int $good_id 商品
 * @property string $content 内容
 * @property string $reply 回复
 * @property int $audit 审核状态
 * @property int $status
 * @property int $is_hide 是否匿名
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at
 */
class Comment extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_Id', 'good_id', 'audit', 'status', 'is_hide', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['content', 'reply'], 'string', 'max' => 511],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_Id' => '用户',
            'good_id' => '商品',
            'content' => '内容',
            'reply' => '回复',
            'audit' => '审核状态',
            'status' => '审核状态',
            'is_hide' => '是否匿名',
            'sort' => '排序',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
