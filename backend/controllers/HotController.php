<?php

namespace backend\controllers;

use common\models\app\Goods;
use Yii;
use common\models\app\Banner;
use common\components\Curd;
use common\models\base\SearchModel;

/**
* Banner
*
* Class BannerController
* @package backend\controllers
*/
class HotController extends BaseController
{
    use Curd;

    /**
    * @var Banner
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
        $dataProvider->query->andWhere(['is_hot' => 1]);
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
        $model = new Goods();
        if (Yii::$app->request->isPost) {
            $goods = Goods::findOne(Yii::$app->request->post('Goods')['id']);
            $goods->is_hot = 1;
            $goods->save(false);
            return $this->redirect(['index']);
        }

        $ids = Goods::getSelectOptions(['!=','is_hot',1]);
        return $this->render($this->action->id, [
            'model' => $model,
            'goods' => $ids
        ]);
    }
    /**
     * 删除
     *
     * @param $id
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_hot = 0;
        if ($model->save(false)) {
            return $this->message("删除成功", $this->redirect(['index']));
        }

        return $this->message("删除失败", $this->redirect(['index']), 'error');
    }
}
