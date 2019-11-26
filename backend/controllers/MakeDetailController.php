<?php

namespace backend\controllers;

use Yii;
use common\models\app\MakeDetail;
use common\components\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;

/**
* MakeDetail
*
* Class MakeDetailController
* @package backend\controllers
*/
class MakeDetailController extends BaseController
{
    use Curd;

    /**
    * @var MakeDetail
    */
    public $modelClass = MakeDetail::class;


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

        $dataProvider->query->andWhere(['make_id' => Yii::$app->request->get('make_id')]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}
