<?php

namespace backend\controllers;

use common\models\app\Goods;
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
        $package_id = Yii::$app->request->get('package_id');

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['package_id' => Yii::$app->request->get('package_id')]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'package_id' => $package_id
        ]);
    }
    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit($package_id)
    {

        $model = new PackageGoods();
        $model->package_id = $package_id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index?package_id='.$model->package_id]);
        }
        $not = PackageGoods::find()->where(['package_id' => $model->package_id])->select('goods_id')->column();
        $ids = Goods::getSelectOptions(['not in','id',$not]);

        return $this->render($this->action->id, [
            'model' => $model,
            'goods' => $ids
        ]);
    }
}
