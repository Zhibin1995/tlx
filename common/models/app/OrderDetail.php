<?php

namespace common\models\app;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "order_detail".
 *
 * @property int $id 主键
 * @property int $member_id 用户
 * @property int $order_id 订单
 * @property int $good_id 商品
 * @property int $num 数量
 * @property int $make_status 预约状态 1：待预约 2：待服务 3：待评价 4：已完成
 * @property int $make_time 预约时间
 * @property int $is_refund 是否退款
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class OrderDetail extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'order_id', 'good_id', 'num', 'make_status', 'make_time', 'is_refund', 'status', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'member_id' => '用户',
            'order_id' => '订单',
            'good_id' => '商品',
            'num' => '数量',
            'make_status' => '预约状态',
            'make_time' => '预约时间',
            'is_refund' => '是否退款',
            'status' => '状态(-1:已删除,0:禁用,1:正常)',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
    public function getMakeStatus(){
        $arr = [
            0 => '待支付',
            1 => '待预约',
            2 => '待服务',
            3 => '待评价',
            4 => '已完成'
        ];
        return $arr[$this->make_status];
    }
}
