<?php

namespace api\modules\v1\controllers;

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
            $model->notifyUrl = Url::removeMerchantIdUrl('toFront', ['/api/pay/notify']);
        }
        if (!$model->validate()) {
            return ResultDataHelper::api(422, $this->getError($model));
        }

        return $model->getConfig();
    }
    public function actionNotify(){
        $response = Yii::$app->pay->wechat->notify();

        if ($response->isPaid()) {
            //pay success
            var_dump($response->getRequestData());
        }else{
            //pay fail
        }
    }
}