<?php

namespace common\models\app;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id 主键
 * @property int $member_id 用户
 * @property string $order_no 订单号
 * @property string $transaction_id 三方支付号
 * @property int $type 类型
 * @property int $pay_status 支付状态 0:待支付 1：已支付 2：退款中 3：已退款 4：待评价 5：已完成 6：已取消
 * @property string $username 收货人
 * @property string $userphone 收货电话
 * @property string $address 收货地址
 * @property int $num 数量
 * @property string $amount 价格
 * @property string $remark 备注
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class Order extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'type', 'pay_status', 'num', 'status', 'created_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['order_no', 'transaction_id', 'username', 'userphone', 'address', 'remark'], 'string', 'max' => 255],
        ];
    }
    public function getPayStatus(){
        $arr = [
            0 => '待支付',
            1 => '已支付',
            2 => '退款中',
            3 => '已退款',
            4 => '待评价',
            5 => '已完成',
            6 => '已取消'
        ];
        return $arr[$this->pay_status];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'member_id' => '用户',
            'order_no' => '订单号',
            'transaction_id' => '三方支付号',
            'type' => '类型',
            'pay_status' => '支付状态',
            'username' => '收货人',
            'userphone' => '收货电话',
            'address' => '收货地址',
            'num' => '数量',
            'amount' => '价格',
            'remark' => '备注',
            'status' => '状态(-1:已删除,0:禁用,1:正常)',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
