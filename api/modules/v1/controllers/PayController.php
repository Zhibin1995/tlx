<?php

namespace api\modules\v1\controllers;

use common\models\app\Order;
use common\models\app\OrderDetail;
use common\models\app\SysSet;
use Yii;
use api\controllers\OnAuthController;
use common\enums\PayEnum;
use common\helpers\Url;
use common\models\forms\PayForm;
use common\helpers\ResultDataHelper;

/**
 * 公用支付生成
 *
 * Class PayController
 * @package api\modules\v1\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class PayController extends OnAuthController
{
    /**
     * @var PayForm
     */
    public $modelClass = PayForm::class;

    protected $optional = ['create','notify'];


    /**
     * 生成支付参数
     *
     * @return array|mixed|\yii\db\ActiveRecord
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionCreate()
    {
        $post = $this->getPost();
        /* @var $model PayForm */
        $model = new $this->modelClass();
        $model->attributes = $post;
        $model->member_id = $post['member_id'];

        if (isset(PayEnum::$payTypeAction[$model->payType])) {
            $model->notifyUrl = Url::removeMerchantIdUrl('toFront', ['/api/v1/pay/notify']);
        }
        if (!$model->validate()) {
            return ResultDataHelper::api(422, $this->getError($model));
        }

        return $model->getConfig();
    }
    public function actionNotify(){
        $response = Yii::$app->pay->wechat->notify();
        $post_data = $response->getRequestData();
        $order_status = Order::find()->where(['order_no'=>$post_data['out_trade_no']])->one();

        if($post_data['return_code']=='SUCCESS' && $order_status){
            /*
            * 首先判断，订单是否已经更新为ok，因为微信会总共发送8次回调确认
            * 其次，订单已经为ok的，直接返回SUCCESS
            * 最后，订单没有为ok的，更新状态为ok，返回SUCCESS
            */
            if($order_status->pay_status === 1){
                $this->return_success();
            }else{
                $order_status->pay_status = 1;
                $order_status->transaction_id = $post_data['transaction_id'];
                OrderDetail::updateAll(['make_status' => 1] ,['order_id' => $order_status->id]);
                if($order_status->save(false)){
                    $sys = SysSet::findOne(1);
                    $sys->serve_num +=1;
                    $sys->save(false);
                    $this->return_success();
                }
            }
        }else{
            echo '微信支付失败';
        }
        echo '{ "code":0 }';die;
    }
    //支付成功返回
    private function return_success(){
        $return['return_code'] = 'SUCCESS';
        $return['return_msg'] = 'OK';
        $xml_post = '<xml>
                    <return_code>'.$return['return_code'].'</return_code>
                    <return_msg>'.$return['return_msg'].'</return_msg>
                    </xml>';
        echo $xml_post;exit;
    }
}