<?php

namespace backend\controllers;

use Yii;
use common\models\app\Order;
use common\components\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;
use common\helpers\ExcelHelper;
/**
* Order
*
* Class OrderController
* @package app\controllers
*/
class OrderController extends BaseController
{
    use Curd;

    /**
    * @var Order
    */
    public $modelClass = Order::class;


    /**
    * 首页
    *
    * @return string
    * @throws \yii\web\NotFoundHttpException
    */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    public function actionExport(){
        $header = [
            ['ID', 'id', 'text'],
            ['用户', 'member_id','function', function ($model) {
                return \common\models\app\Member::find()->select('nickname')->where(['id' => $model->member_id])->scalar();
            }], // 规则不填默认text
            ['订单号', 'order_no', 'function',function ($model) {

                return ' '.$model->order_no;
            }],
            ['类型', 'type', 'function',function ($model) {
                $arr = [
                    1 => '商品',
                    2 => '套餐'
                ];
                return $arr[$model->type];
            }],
            ['支付状态', 'type', 'function',function ($model) {
            return $model->getPayStatus();
            }],
            ['数量', 'num', 'text'],
            ['价格', 'amount', 'text'],
            ['创建时间', 'created_at', 'date', 'Y-m-d H:i:s'],
        ];

        $list = Order::find()->all();
        return ExcelHelper::exportData($list, $header);
    }
}
