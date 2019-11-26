<?php

namespace console\controllers;

use common\models\app\Order;
use Yii;
use yii\console\Controller;
use common\enums\StatusEnum;
use common\models\wechat\MassRecord;

/**
 * Class SendMessageController
 * @package console\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class OrderController extends Controller
{
    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function actionIndex()
    {
        $ids = Order::find()
            ->where(['pay_status' => 0])
            ->andWhere(['<=', 'created_at', time()-180])
            ->select('id')->column();
        Order::updateAll(['pay_status' => 6],['in','id',$ids]);

        $this->stdout(date('Y-m-d H:i:s') . ' --- ' . '订单自动过期成功:' .json_encode($ids) . PHP_EOL);

        exit();
    }
}