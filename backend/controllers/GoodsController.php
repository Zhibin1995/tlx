<?php

namespace backend\controllers;

use common\models\app\Category;
use Yii;
use common\models\app\Goods;
use common\components\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;

/**
* Goods
*
* Class GoodsController
* @package backend\controllers
*/
class GoodsController extends BaseController
{
    use Curd;

    /**
    * @var Goods
    */
    public $modelClass = Goods::class;


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

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id', null);
        $model = $this->findModel($id);
        if($model->url){
            $model->url = explode(',',$model->url);
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->url = implode(',',$model->url);
            $model->save();
            return $this->redirect(['index']);
        }

        $category = Category::getSelectOptions(['>','status',0]);
        return $this->render($this->action->id, [
            'model' => $model,
            'category' => $category
        ]);
    }
}
