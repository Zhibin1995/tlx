<?php

namespace common\models\forms;

use common\models\app\Address;
use common\models\app\Goods;
use common\models\app\Member;
use common\models\app\Order;
use common\models\app\OrderDetail;
use common\models\app\Package;
use common\models\app\PackageGoods;
use Yii;
use yii\base\Model;
use yii\helpers\Json;
use yii\web\UnprocessableEntityHttpException;
use common\enums\PayEnum;

/**
 * Class PayForm
 * @package api\modules\v1\forms
 * @author jianyan74 <751393839@qq.com>
 */
class PayForm extends Model
{
    public $orderGroup;
    public $payType;
    public $tradeType = 'default';
    public $data; // json数组
    public $member_id;
    public $returnUrl;
    public $notifyUrl = "/api/pay/notify";
    public $address_id;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['orderGroup', 'payType', 'data', 'tradeType', 'member_id'], 'required'],
            [['orderGroup'], 'in', 'range' => array_keys(PayEnum::$orderGroupExplain)],
            [['payType'], 'in', 'range' => array_keys(PayEnum::$payTypeExplain)],
            [['notifyUrl', 'returnUrl'], 'string'],
            [['tradeType'], 'verifyTradeType'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'orderGroup' => '订单组别',
            'data' => '组别对应数据',
            'payType' => '支付类别',
            'tradeType' => '交易类别',
            'member_id' => '用户id',
            'returnUrl' => '跳转地址',
            'notifyUrl' => '回调地址',
        ];
    }

    /**
     * 校验交易类型
     */
    public function verifyTradeType($attribute)
    {
        switch ($this->payType) {
            case PayEnum::PAY_TYPE :
                break;
            case PayEnum::PAY_TYPE_WECHAT :
                if (!in_array($this->tradeType, ['native', 'app', 'js', 'pos', 'mweb'])) {
                    $this->addError($attribute, '微信交易类型不符');
                }
                break;
            case PayEnum::PAY_TYPE_ALI :
                if (!in_array($this->tradeType, ['pc', 'app', 'f2f', 'wap'])) {
                    $this->addError($attribute, '支付宝交易类型不符');
                }
                break;
            case PayEnum::PAY_TYPE_MINI_PROGRAM :
                break;
            case PayEnum::PAY_TYPE_UNION :
                if (!in_array($this->tradeType, ['app', 'html'])) {
                    $this->addError($attribute, '银联交易类型不符');
                }
                break;
        }
    }

    /**
     * @return array
     * @throws UnprocessableEntityHttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \yii\base\InvalidConfigException
     */
    public function getConfig()
    {
        $action = PayEnum::$payTypeAction[$this->payType];
        $baseOrder = $this->getBaseOrderInfo();

        return Yii::$app->services->pay->$action($this, $baseOrder);
    }

    /**
     * 获取支付基础信息
     *
     * @param $type
     * @param $data
     * @return array
     */
    protected function getBaseOrderInfo()
    {
        $data = $this->data;
        switch ($this->orderGroup) {
            case PayEnum::ORDER_GROUP_GOODS :
                $num = $amount = 0;
                //$address = Address::findOne($this->address_id);
                $order_model = new Order();
                $order_model->member_id = $this->member_id;
                $order_model->order_no = $this->createOrderNo();
                //$order_model->username = $address->realname;
                //$order_model->userphone = $address->mobile;
                //$order_model->address = $address->address_name.$address->address_details;
                $order_model->num = $num;
                $order_model->amount = $amount;
                $order_model->save();
                // TODO 查询订单获取订单信息
                foreach ($data as $v){
                    $order_detail = new OrderDetail();
                    $order_detail->member_id = $this->member_id;
                    $order_detail->order_id = $order_model->id;
                    $order_detail->good_id = $v['good_id'];
                    $order_detail->num = $v['num'];
                    $order_detail->save();
                    $amount += Goods::find()->where(['id' => $v['good_id']])->select('price')->scalar() * $v['num'];
                    $num+= $v['num'];
                }
                $order_model->type = 1;
                $order_model->num = $num;
                $order_model->amount = $amount;
                $order_model->save();
                $member = Member::findOne($this->member_id);
//                $totalFee = (int)$amount*100;
                $totalFee =1;
                $order = [
                    'body' => 'buy',
                    'total_fee' => $totalFee,
                    'open_id' => $member->open_id
                ];
                break;
            case  PayEnum::ORDER_REPAY:
                $order_id = $data['id'];
                $order_model = Order::findOne($order_id);
                $order_model->order_no = $this->createOrderNo();
                $order_model->save();
                $member = Member::findOne($this->member_id);
//                $totalFee = (int)$package->price*100;
                $totalFee = 1;
                $order = [
                    'body' => 'repay',
                    'total_fee' => $totalFee,
                    'open_id' => $member->open_id
                ];
                break;
            default:
                $num = $amount = 0;
                //$address = Address::findOne($this->address_id);
                $order_model = new Order();
                $order_model->member_id = $this->member_id;
                $order_model->order_no = $this->createOrderNo();
                //$order_model->username = $address->realname;
                //$order_model->userphone = $address->mobile;
                //$order_model->address = $address->address_name.$address->address_details;
                $order_model->num = $num;
                $order_model->amount = $amount;
                $order_model->save();
                $package_id = $data['package_id'];
                $package = Package::findOne($package_id);
                $package_goods = PackageGoods::find()->where(['package_id' => $package_id])->all();
                foreach ($package_goods as $v){
                    $order_detail = new OrderDetail();
                    $order_detail->member_id = $this->member_id;
                    $order_detail->order_id = $order_model->id;
                    $order_detail->good_id = $v->goods_id;
                    $order_detail->num = $v->num;
                    $order_detail->save();
                }
                $order_model->package_id = $package_id;
                $order_model->type = 2;
                $order_model->num = 1;
                $order_model->amount = $package->price;
                $order_model->save();
                $member = Member::findOne($this->member_id);
//                $totalFee = (int)$package->price*100;
                $totalFee = 1;
                $order = [
                    'body' => 'package',
                    'total_fee' => $totalFee,
                    'open_id' => $member->open_id
                ];
                break;
        }

        // 也可直接查数据库对应的关联ID，这样子一个订单只生成一个支付操作ID 增加下单率
        // Yii::$app->services->pay->findByOutTradeNo($order->out_trade_no);

        $order['out_trade_no'] = $order_model->order_no;

        // 必须返回 body、total_fee、out_trade_no
        return $order;
    }
    public function createOrderNo(){
        $no = date('YmdHis',time()).rand(1000,9999);
        return $no;
    }
}
