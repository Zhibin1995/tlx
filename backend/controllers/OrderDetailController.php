<?php

namespace backend\controllers;

use Yii;
use common\models\app\OrderDetail;
use common\components\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;

/**
* OrderDetail
*
* Class OrderDetailController
* @package app\controllers
*/
class OrderDetailController extends BaseController
{
    use Curd;

    /**
    * @var OrderDetail
    */
    public $modelClass = OrderDetail::class;


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
}
