<?php

namespace backend\controllers;

use common\models\app\ShopComment;
use Yii;
use common\models\app\Shop;
use common\components\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;

/**
* Shop
*
* Class ShopController
* @package backend\controllers
*/
class ShopController extends BaseController
{
    use Curd;

    /**
    * @var Shop
    */
    public $modelClass = Shop::class;


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
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->id){
                $model->password = md5(md5($model->password));
                $model->save();
                $shop_comment = new ShopComment();
                $shop_comment->shop_id = $model->id;
                $shop_comment->serve = 5;
                $shop_comment->wear = 5;
                $shop_comment->art = 5;
                $shop_comment->flow = 5;
                $shop_comment->total = 20;
                $shop_comment->save();
            }
            $model->save();
            return $this->redirect(['index']);
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }
}
