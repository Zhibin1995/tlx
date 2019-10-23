<?php

namespace app\controllers;

use Yii;
use common\models\app\PackageGoods;
use common\components\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;

/**
* PackageGoods
*
* Class PackageGoodsController
* @package app\controllers
*/
class PackageGoodsController extends BaseController
{
    use Curd;

    /**
    * @var PackageGoods
    */
    public $modelClass = PackageGoods::class;


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
