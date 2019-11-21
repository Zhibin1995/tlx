<?php

namespace backend\controllers;

use Yii;
use common\models\app\CommentImg;
use common\components\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;

/**
* CommentImg
*
* Class CommentImgController
* @package app\controllers
*/
class CommentImgController extends BaseController
{
    use Curd;

    /**
    * @var CommentImg
    */
    public $modelClass = CommentImg::class;


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
        $dataProvider->query->andWhere(['comment_Id' => Yii::$app->request->get('comment_id')]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}
