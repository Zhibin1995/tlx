<?php

namespace services\common;

use Yii;
use common\enums\PayEnum;
use common\components\Service;
use common\models\common\PayLog;
use common\helpers\StringHelper;
use common\models\forms\PayForm;
use common\helpers\ArrayHelper;

/**
 * Class PayService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PayService extends Service
{
    /**
     * @param PayForm $payForm
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function wechat(PayForm $payForm, $baseOrder)
    {
        // 生成订单
        $order = [
            'body' => $baseOrder['body'], // 内容
            'out_trade_no' => $baseOrder['out_trade_no'], // 订单号
            'total_fee' => $baseOrder['total_fee'],
            'notify_url' => $payForm->notifyUrl, // 回调地址
        ];

        //  判断如果是js支付
        if ($payForm->tradeType == 'js') {
            $order['open_id'] = '';
        }

        //  判断如果是刷卡支付
        if ($payForm->tradeType == 'pos') {
            $order['auth_code'] = '';
        }

        // 交易类型
        $tradeType = $payForm->tradeType;
        return Yii::$app->pay->wechat->$tradeType($order);
    }

    /**
     * @param PayForm $payForm
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function alipay(PayForm $payForm, $baseOrder)
    {
        // 配置
        $config = [
            'notify_url' => $payForm->notifyUrl, // 支付通知回调地址
            'return_url' => $payForm->returnUrl, // 买家付款成功跳转地址
            'sandbox' => false
        ];

        // 生成订单
        $order = [
            'out_trade_no' => $baseOrder['out_trade_no'],
            'total_amount' => $baseOrder['total_fee'] / 100,
            'subject' => $baseOrder['body'],
        ];

        // 交易类型
        $tradeType = $payForm->tradeType;
        return [
            'config' => Yii::$app->pay->alipay($config)->$tradeType($order)
        ];
    }

    /**
     * @param PayForm $payForm
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function union(PayForm $payForm, $baseOrder)
    {
        // 配置
        $config = [
            'notify_url' => $payForm->notifyUrl, // 支付通知回调地址
            'return_url' => $payForm->returnUrl, // 买家付款成功跳转地址
        ];

        // 生成订单
        $order = [
            'orderId' => $baseOrder['out_trade_no'], //Your order ID
            'txnTime' => date('YmdHis'), //Should be format 'YmdHis'
            'orderDesc' => $baseOrder['body'], //Order Title
            'txnAmt' => $baseOrder['total_fee'], //Order Total Fee
        ];

        // 交易类型
        $tradeType = $payForm->tradeType;
        return Yii::$app->pay->union($config)->$tradeType($order);
    }

    /**
     * @param PayForm $payForm
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \yii\base\InvalidConfigException
     */
    public function miniProgram(PayForm $payForm, $baseOrder)
    {
        // 设置appid
        Yii::$app->params['wechatPaymentConfig'] = ArrayHelper::merge(Yii::$app->params['wechatPaymentConfig'], [
            'app_id' => Yii::$app->debris->config('miniprogram_appid')
        ]);

        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";

        $appid = Yii::$app->params['wechatPaymentConfig']['app_id'];
        $mch_id = Yii::$app->params['wechatPaymentConfig']['mch_id'];
        $key = Yii::$app->params['wechatPaymentConfig']['key'];
        $nonce_str = $this->createNonceStr(32);
        $order_arr = array(
            'appid'=>$appid,
            'mch_id'=>$mch_id,
            'nonce_str'=>$nonce_str,
            'body'=>$baseOrder['body'],
            'out_trade_no'=>$baseOrder['out_trade_no'],
            'total_fee'=>$baseOrder['total_fee'],
            'spbill_create_ip'=>$_SERVER["REMOTE_ADDR"],
            'notify_url'=>$payForm->notifyUrl,
            'trade_type'=>"JSAPI",
            'openid'=>$baseOrder['open_id'],
        );
        $sign = $this->MakeSign($order_arr,$key);
        $order_arr['sign'] = $sign;
        $xml = $this->array_to_xml($order_arr);
        $pay_res_xml = $this->httpRequest($url,$xml);
        $pay_res_arr = $this->xml_to_array($pay_res_xml);
        if($pay_res_arr['return_code'] =='SUCCESS' && $pay_res_arr['result_code'] =='SUCCESS'){
            $prepay_id = $pay_res_arr['prepay_id'];
            $nonceStr = $pay_res_arr['nonce_str'];
            if($prepay_id && $nonceStr){
                $this_time = time();
                $ret_pay_data =array(
                    'appId'=>$appid,
                    'timeStamp'=>$this_time,
                    'nonceStr'=>$nonceStr,
                    'package'=>'prepay_id='.$prepay_id,
                    'signType'=>'MD5',
                );
                $paySign = $this->MakeSign($ret_pay_data,$key);
                $return_pay = array(
                    'timeStamp'=>$this_time,
                    'nonceStr' => $nonceStr,
                    'signType' => 'MD5',
                    'paySign' => $paySign,
                    'package' => 'prepay_id='.$prepay_id
                );
                echo json_encode(array('code'=>0,'message'=>'','data'=>$return_pay));die;
            }else{
                echo json_encode(array('code'=>1,'message'=>'生成订单失败'));die;
            }
        }else{
            echo json_encode(array('code'=>1,'message'=>'提交订单失败'));die;
        }
    }

    /**
     * 获取订单支付日志编号
     *
     * @param int $payFee 单位分
     * @param string $orderSn 关联订单号
     * @param int $orderGroup 订单组别 如果有自己的多种订单类型请去\common\models\common\PayLog里面增加对应的常量
     * @param int $payType 支付类型 1:微信;2:支付宝;3:银联;4:微信小程序
     * @param string $tradeType 支付方式
     * @return string
     */
    public function getOutTradeNo($totalFee, string $orderSn, int $payType, $tradeType = 'JSAPI', $orderGroup = 1)
    {
        $payModel = new PayLog();
        $payModel->out_trade_no = StringHelper::randomNum(time());
        $payModel->total_fee = $totalFee;
        $payModel->order_sn = $orderSn;
        $payModel->order_group = $orderGroup;
        $payModel->pay_type = $payType;
        $payModel->trade_type = $tradeType;
        $payModel->save();

        return $payModel->out_trade_no;
    }

    /**
     * @param $outTradeNo
     * @return array|null|\yii\db\ActiveRecord|PayLog
     */
    public function findByOutTradeNo($outTradeNo)
    {
        return PayLog::find()
            ->where(['out_trade_no' => $outTradeNo])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * 支付通知回调
     *
     * @param PayLog $log
     * @param string $paymentType 支付类型
     * @return bool
     */
    public function notify(PayLog $log, $paymentType)
    {
        $log->ip = ip2long(Yii::$app->request->userIP);
        $log->save();

        switch ($log->order_group) {
            case PayEnum::ORDER_GROUP :
                // TODO 处理订单
                return true;
                break;
            case PayEnum::ORDER_GROUP_RECHARGE :
                // TODO 处理充值信息
                return true;
                break;
        }
    }
    public function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    /**
     * 生成签名, $KEY就是支付key
     * @return 签名
     */
    public function MakeSign($params,$KEY){
        //签名步骤一：按字典序排序数组参数
        ksort($params);
        $string = self::ToUrlParams($params);  //参数进行拼接key=value&k=v
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$KEY;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
    public function ToUrlParams( $params ){
        $string = '';
        if( !empty($params) ){
            $array = array();
            foreach( $params as $key => $value ){
                $array[] = $key.'='.$value;
            }
            $string = implode("&",$array);
        }
        return $string;
    }
    public function array_to_xml( $params ){
        if(!is_array($params)|| count($params) <= 0)
        {
            return false;
        }
        $xml = "<xml>";
        foreach ($params as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
    public function httpRequest($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty ($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($data)
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    public function xml_to_array($xml){
        if(!$xml){
            return false;
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }
}
