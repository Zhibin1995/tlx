<?php

namespace common\models\app;

use Yii;

/**
 * This is the model class for table "order_make".
 *
 * @property int $id
 * @property int $member_id 顾客
 * @property string $detail_ids 图片
 * @property int $address_id 地址
 * @property string $remark 备注
 * @property int $date 日期
 * @property int $start 开始时间
 * @property int $end 结束时间
 * @property int $finsh 完成时间
 * @property int $shop_id 完成人
 * @property string $code 预约码
 * @property int $make_status 预约状态
 * @property int $refund_status 退款状态
 * @property int $status
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at
 */
class OrderMake extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_make';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'address_id', 'date', 'start', 'end', 'finsh', 'shop_id', 'make_status', 'refund_status', 'status', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['detail_ids', 'remark'], 'string', 'max' => 1024],
            [['code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '顾客',
            'detail_ids' => '图片',
            'address_id' => '地址',
            'remark' => '备注',
            'date' => '日期',
            'start' => '开始时间',
            'end' => '结束时间',
            'finsh' => '完成时间',
            'shop_id' => '完成人',
            'code' => '预约码',
            'make_status' => '预约状态',
            'refund_status' => '退款状态',
            'status' => 'Status',
            'sort' => '排序',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
    }
}
